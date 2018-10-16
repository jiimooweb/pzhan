<?php

namespace App\Http\Controllers\Api\Fans;

use App\Utils\Module;
use App\Models\Social;
use App\Services\Qiniu;
use App\Services\Token;
use Illuminate\Http\Request;
use App\Models\SocialComment;
use App\Http\Controllers\Controller;

class SocialController extends Controller
{
    public function index() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $socials = Social::with(['photos'])->withCount(['likeFans', 'comments'])->orderBy('created_at', 'desc')->paginate(10);
        foreach($socials as &$social) {
            $social->like = $social->isLike($fan_id) ? 1 : 0;
        }
        return response()->json(['status' => 'success', 'data' => $socials]);   
    }

    public function store(SocialRequest $request) 
    {   
        $data = request()->all();  
        $data['fan_id'] = request('fan_id') ?? Token::getUid(); 
        if(Social::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show()
    {
        $social = Social::where('id', request()->social)->with(['photo'])->first();
        $social->like = $social->isLike($fan_id) ? 1 : 0;        
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

    public function comments()
    {
        $comments = SocialComment::where('social_id', request('social'))->with(['fan', 'toFan'])->orderBy('created_at', 'asc')->get();
        $comments = \App\Utils\Common::getCommentTree($comments->toArray());
        return response()->json(['status' => 'success', 'data' => $comments]);
    }

    public function comment(Social $social)
    {
        $data =request()->all();

        $comment = SocialComment::create($data);

        if($comment) {

            $fan_id = request('fan_id') ?? Token::getUid(); 
            //如果是本人发的评论，则不做回复，或者本人回复他人，并发通知给被回复人
            if($fan_id == $data['fan_id']) {
                $fan_id = $data['to_fan_id'];
            }

            if($fan_id > 0) {
                //添加通知
                $notice = [
                    'fan_id' => $fan_id,
                    'module_id' => $social->id,
                    'module' => Module::Social,
                    'type' => 1
                ];

                Notice::create($notice);
            }
            
            return response()->json(['status' => 'success', 'data' => $comment]);
        }

        return response()->json(['status' => 'error']);
        
    }

    public function like(Social $social)
    {
        $data =request()->all();

        $fan_id = request('fan_id') ?? Token::getUid(); 
        
        $like = SocialComment::create(['fan_id' => $fan_id, 'social_id' => $social->id]);
        
        if($like ) {
            //添加通知
            $notice = [
                'fan_id' => $social->fan_id,
                'module_id' => $social->id,
                'module' => Module::Social,
                'type' => 0
            ];

            Notice::create($notice);
            
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error']);
    }

}
