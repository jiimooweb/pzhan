<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Album;
use App\Services\Token;
use Illuminate\Http\Request;
use App\Http\Requests\AlbumRequest;
use App\Http\Controllers\Controller;

class AlbumController extends Controller
{
    public function index() 
    {
        $albums = Album::where('fan_id', Token::getUid())->get();
        return response()->json(['status' => 'success', 'data' => $albums]);   
    }

    public function store(AlbumRequest $request) 
    {   
        $data = request()->all();  
        $data['fan_id'] = Token::getUid();  
        if(Album::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show()
    {
        $album = Album::where('id', request()->album)->with(['photo']);
        $status = $album ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $album]);   
    }

    public function update(AlbumRequest $request)
    {
        $data = request()->all();                      
        if(Album::where('id', request()->album)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);               
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(Album::where('id', request()->album)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    public function change() 
    {
        if(Album::where('id', request('id'))->update(['hidden' => request('hidden')])) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);     
    }

}
