<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Social;
use App\Services\Qiniu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SocialController extends Controller
{
    public function index() 
    {
        $socials = Social::with(['photo'])->orderBy('created_at', 'desc')->paginate(config('common.pagesize'));
        return response()->json(['status' => 'success', 'data' => $socials]);   
    }

    public function store(SocialRequest $request) 
    {   
        $data = request()->all();  
        $data['fan_id'] = Token::getUid();  
        if(Social::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show()
    {
        $social = Social::where('id', request()->social)->with(['photo']);
        $status = $social ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $social]);   
    }

    public function update(SocialRequest $request)
    {
        $data = request()->all();                      
        if(Social::where('id', request()->social)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);               
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(Social::where('id', request()->social)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    public function change() 
    {
        if(Social::where('id', request('id'))->update(['hidden' => request('hidden')])) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);     
    }

}
