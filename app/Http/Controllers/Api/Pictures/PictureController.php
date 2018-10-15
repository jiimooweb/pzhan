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
        $tag_ids = request('tag_ids');
        $fan_id = request('fan_id') ?? Token::getUid();
        $pictures = Picture::with(['tags' => function ($query) use ($tag_ids){
            $query->when($tag_ids, function($query) use ($tag_ids) {
                return $query->whereIn('id', $tag_ids);
            })->select('tags.id', 'tags.name');
        }])->withCount(['likeFans', 'collectFans'])->paginate(30); 

        foreach($pictures as &$picture) {
            $picture->collect = $picture->isCollect($fan_id) ? 1 : 0;
            $picture->like = $picture->isLike($fan_id) ? 1 : 0;
        }
        
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
        $limit = 15;
        $tags = PictureTag::where('picture_id',$picture->id)->get()->pluck('tag_id');

        foreach($tags as $tag) {
            $picture_ids = PictureTag::whereIn('tag_id', $tag)->inRandomOrder()->limit($limit)->get()->pluck('picture_id');
        }
        dd($picture_ids);
        $status = $picture ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $picture]);   
    }
    
}
