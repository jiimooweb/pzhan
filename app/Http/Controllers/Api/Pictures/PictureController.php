<?php

namespace App\Http\Controllers\Api\Pictures;

use App\Models\Picture;
use App\Services\Qiniu;
use App\Services\Token;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PictureRequest;

class PictureController extends Controller
{
    public function index() 
    {
        $pictures = Picture::with(['tags' => function ($query) use ($tag_id){
            $query->when($tag_id, function($query) use ($tag_id) {
                return $query->where('tag_id', $tag_id);
            })->select('tag.id', 'tag.name');
        }])->get(); 
        
        return response()->json(['status' => 'success', 'data' => $pictures]);
    }

    public function show()
    {
        $fan_id = Token::getUid();
        $picture = Picture::with(['tags' => function ($query){
            $query->select('tag.id', 'tag.name');
        }])->find(request()->picture);
        $status = $picture ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $picture]);   
    }

    public function store(PictureRequest $request) 
    {
        $picture = Picture::create($request);

        if($picture) {

            return response()->json(['status' => 'success', 'msg' => '新增成功!']);               
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);               
    }


    public function update(PictureRequest $request) 
    {
        if(Picture::where('id', request()->picture)->update(request()->all())){
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                  
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);               
    }


    public function destroy()
    {
        // TODO:判断删除权限
        if(Picture::where('id', request()->picture)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);   
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }
    
}
