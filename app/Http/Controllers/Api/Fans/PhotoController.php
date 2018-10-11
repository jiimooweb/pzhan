<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Photo;
use App\Services\Qiniu;
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;
use App\Http\Controllers\Controller;

class PhotoController extends Controller
{
    public function index() 
    {
        $photo = Photo::where('album_id', request('album_id'))->get();
        return response()->json(['status' => 'success', 'data' => $photos]);   
    }

    public function store(PhotoRequest $request) 
    {   
        $data = request()->all();  
        $data['fan_id'] = Token::getUid(); 
        if(Photo::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show()
    {
        $photo = Photo::where('id', request()->photo)->with(['album']);
        $status = $photo ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $photo]);   
    }

    public function update(PhotoRequest $request)
    {
        $data = request()->all();                      
        if(Photo::where('id', request()->photo)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);               
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(Photo::where('id', request()->photo)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    public function change() 
    {
        if(Photo::where('id', request('id'))->update(['hidden' => request('hidden')])) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);     
    }

}
