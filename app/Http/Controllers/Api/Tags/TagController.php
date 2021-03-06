<?php

namespace App\Http\Controllers\Api\Tags;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index() 
    {
        $keyword = request('keyword');
        $tags = Tag::when($keyword, function($query) use ($keyword) {
            return $query->where('name', 'like', '%'.$keyword.'%');
        })->paginate(50);
        return response()->json(['status' => 'success', 'data' => $tags]);   
    }

    public function store(TagRequest $request) 
    {   
        $data = request()->all();  
        if(Tag::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show()
    {
        $tag = Tag::find(request()->tag);
        $status = $tag ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $tag]);   
    }

    public function update(TagRequest $request)
    {
        $data = request()->all();                      
        if(Tag::where('id', request()->tag)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);               
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(Tag::where('id', request()->tag)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    public function all()
    {
        $tags = Tag::get();
        return response()->json(['status' => 'success', 'data' => $tags]);
    }

    public function random() 
    {
        $tags = Tag::where('hidden', 0)->inRandomOrder()->limit(10)->get();
        return response()->json(['status' => 'success', 'data' => $tags]);        
    }

    public function changeHidden(Tag $tag) 
    {
        $tag->hidden = request('hidden');
        if($tag->save()) {
            return response()->json(['status' => 'success']);        
        }

        return response()->json(['status' => 'error']);        
    }

    public function changeHiddenAll() 
    {
        $hidden = request('hidden');        
        Tag::where('id', '>', 0)->update(['hidden' => $hidden]);
        return response()->json(['status' => 'success']);     
    }

    public function getHots()
    {
        $tags = Tag::where('hidden', 0)->orderBy('click','desc')->limit(50)->get()->toArray();
        shuffle($tags);
        $tags = array_slice($tags, 0, 15);
        return response()->json(['status' => 'success', 'data' => $tags]);        

    }
    
}
