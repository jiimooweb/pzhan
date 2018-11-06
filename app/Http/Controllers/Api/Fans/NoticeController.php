<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Special;
use App\Utils\Module;
use App\Models\Social;
use App\Services\Token;
use App\Models\LikeNotice;
use Illuminate\Http\Request;
use App\Models\CommentNotice;
use App\Http\Controllers\Controller;

class NoticeController extends Controller
{
    public function index() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $commentNotices = CommentNotice::where(['fan_id'=>$fan_id,'status' => 0])->count();
        $likeNotices = LikeNotice::where(['fan_id'=>$fan_id,'status' => 0])->count(); 
        $noticeCount = $commentNotices + $likeNotices;
        return response()->json(['status' => 'success', 'count' => $noticeCount, 'like_count' => $likeNotices, 'comment_count' => $commentNotices]);  
    }

    public function comment() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $notices = CommentNotice::where('fan_id',$fan_id)->with(['fan', 'fromFan', 'toFan'])->orderBy('created_at', 'desc')->paginate(10); 
        foreach($notices as &$notice) {
            if($notice->module == Module::Social) {
                $notice->module_content = Social::where('id', $notice->module_id)->with('fan')->first(); 
            }
            if($notice->module == Module::Special) {
                $notice->module_content = Special::where('id', $notice->module_id)->with('fan')->first();
            }
        }
        CommentNotice::where(['fan_id' => $fan_id, 'status' => 0])->update(['status' => 1]);
    
        return response()->json(['status' => 'success', 'data' => $notices]);  
    }


    public function like() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $notices = LikeNotice::where('fan_id',$fan_id)->with(['fan', 'fromFan'])->orderBy('created_at', 'desc')->paginate(10); 
        foreach($notices as &$notice) {
            if($notice->module == Module::Social) {
                $notice->module_content = Social::where('id', $notice->module_id)->with('fan')->first(); 
            }
        }
        LikeNotice::where(['fan_id' => $fan_id, 'status' => 0])->update(['status' => 1]);

        return response()->json(['status' => 'success', 'data' => $notices]);  
    }
}
