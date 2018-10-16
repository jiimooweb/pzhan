<?php

namespace App\Http\Controllers\Api\Pictures;

use App\Models\Picture;
use App\Services\Qiniu;
use App\Services\Token;
use App\Models\PictureTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PictureRequest;

class PictureController extends Controller
{
    public function index() 
    {
        $id = request('id');
        $tag_id = request('tag_id');
        $title = request('title');
        $author = request('author');
        $collectOrder = request('collectOrder') ? 'asc' : 'desc';
        $likeOrder = request('likeOrder') ? 'asc' : 'desc';
        $fan_id = request('fan_id') ?? Token::getUid();
        if(isset($tag_id)) {
            $picture_ids = PictureTag::where('tag_id',$tag_id)->get()->pluck('picture_id');
        }
        $pictures = Picture::with(['tags'])->when($id > 0, function($query) use ($id) {
            return $query->where('id', $id);
        })->when($picture_ids, function($query) use ($picture_ids) {
            return $query->whereIn('id', $picture_ids);
        })->when($title, function($query) use ($title) {
            return $query->where('title', 'like', '%'.$title.'%');
        })->when($author, function($query) use ($author) {
            return $query->where('author', 'like', '%'.$author.'%');
        })->withCount(['likeFans', 'collectFans'])->when($collectOrder, function($query) use ($collectOrder){
            return $query->orderBy('collect_fans_count', $collectOrder);
        })->when($likeOrder, function($query) use ($likeOrder){
            return $query->orderBy('like_fans_count', $likeOrder);
        })->paginate(30); 
        
        return response()->json(['status' => 'success', 'data' => $pictures]);
    }

    public function show(Picture $picture)
    {
        $picture = $picture->with(['tags' => function ($query){
            $query->select('tags.id', 'tags.name');
        }])->withCount(['likeFans', 'collectFans'])->first();
        
        $status = $picture ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $picture]);   
    }

    public function store(PictureRequest $request) 
    {
        $tags = $request->tags;

        $picture = Picture::create($request->picture);

        if($picture) {

            if($tags) {
                foreach($tags as $tag) {
                    PictureTag::create(['tag_id' => $tag, 'picture_id' => $picture->id]);
                }
            }

            return response()->json(['status' => 'success', 'msg' => '新增成功!']);               
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);               
    }


    public function update(PictureRequest $request, Picture $picture) 
    {
        $tags = $request->tags;

        if($picture->update($request->picture)){

            if($tags) {
                PictureTag::where('picture_id',$picture->id)->delete();

                foreach($tags as $tag) {
                    PictureTag::create(['tag_id' => $tag, 'picture_id' => $picture->id]);
                }
            }

            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                  
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);               
    }


    public function destroy(Picture $picture)
    {
        // TODO:判断删除权限
        if($picture->delete()) {

            PictureTag::where('picture_id',$picture->id)->delete();

            return response()->json(['status' => 'success', 'msg' => '删除成功！']);   
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

    public function collect(Picture $picture) 
    {
        $param = [
            'fan_id' => request('fan_id') ?? Token::getUid(),
            'picture_id' => $picture->id
        ];

        if(CollectPicture::firstOrCreate($param)) {
            return response()->json(['status' => 'success', 'msg' => '收藏成功！']);  
        }

        return response()->json(['status' => 'error', 'msg' => '收藏失败！']);  
    }

    public function uncollect(Picture $picture) 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        if($picture->collect($fan_id)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '取消成功！']);  
        }

        return response()->json(['status' => 'error', 'msg' => '取消失败！']);  
    }

    public function like(Picture $picture) 
    {
        $param = [
            'fan_id' => request('fan_id') ?? Token::getUid(),
            'picture_id' => $picture->id
        ];

        if(LikePicture::firstOrCreate($param)) {
            return response()->json(['status' => 'success', 'msg' => '点赞成功！']);  
        }

        return response()->json(['status' => 'error', 'msg' => '点赞失败！']);  
    }

    public function unlike(Picture $picture) 
    {
        $fan_id = request('fan_id') ?? Token::getUid();        
        if($picture->like($fan_id)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '取消成功！']);  
        }

        return response()->json(['status' => 'error', 'msg' => '取消失败！']);  
    }

    public function app_list()
    {
        $fan_id = request('fan_id') ?? Token::getUid();                
        $limit = 15;
        $pictures = Picture::withCount(['likeFans', 'collectFans'])->inRandomOrder()->limit($limit)->get(); 

        foreach($pictures as &$picture) {
            $picture->collect = $picture->isCollect($fan_id) ? 1 : 0;
            $picture->like = $picture->isLike($fan_id) ? 1 : 0;
        }
        
        return response()->json(['status' => 'success', 'data' => $pictures]);
    }

    public function app_show(Picture $picture)
    {
        $fan_id = request('fan_id') ?? Token::getUid();

        $picture = $picture->with(['tags' => function ($query){
            $query->select('tags.id', 'tags.name');
        }])->first();

        $picture->collect = $picture->isCollect($fan_id) ? 1 : 0;  //是否收藏
        $picture->like = $picture->isLike($fan_id) ? 1 : 0;   //是否点赞

        //相关推荐
        $recommends = PictureTag::getRecommends($picture->id);
        $status = $picture ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $picture, 'recommends' => $recommends]);   
    }

    public function rank()
    {             
        $collect = request('collect');
        $like = request('like');
        $pictures = Picture::withCount(['likeFans', 'collectFans'])->when($collect, function($query) use ($collectOrder){
            return $query->orderBy('collect_fans_count', 'desc');
        })->when($likeOrder, function($query) use ($like){
            return $query->orderBy('like_fans_count', 'desc');
        })->paginate(20);

        return response()->json(['status' => 'success', 'data' => $pictures]);
        
    }
    
}
