<?php

namespace App\Http\Controllers\Api\Ads;

use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::get();
        return response()->json(['status' => 'success', 'data' => $ads]);                            
    }

    public function store()
    {
        $data = request()->all();
        
        if(Ad::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                            
        }
            
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);  
    }

    public function update()
    {
        $data = request()->all();
        
        if(Ad::where('id', request()->ad)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                            
        }
            
        return response()->json(['status' => 'error', 'msg' => '新增失败！']); 
    }

    public function destroy()
    {
        if(Ad::where('id', request()->ad)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);               
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);  
    }

    public function app()
    {
        $ads = Ad::where('hidden', 0)->get();
        return response()->json(['status' => 'success', 'data' => $ads]); 
    }


}

