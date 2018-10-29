<?php

namespace App\Http\Controllers\Api\Fans;

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
        return response()->json(['status' => 'success', 'data' => $noticeCount]);  
    }

    public function comment() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $notices = CommentNotice::where('fan_id',$fan_id)->with(['fan', 'fromFan', 'toFan'])->paginate(20); 
        foreach($notices as &$notice) {
            if($notice->module == Module::Social) {
                $notice->module_content = Social::where('id', $notice->module_id)->with('fan')->first(); 
            }
        }

        return response()->json(['status' => 'success', 'data' => $notices]);  
    }


    public function like() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $notices = LikeNotice::where('fan_id',$fan_id)->with(['fan', 'fromFan'])->paginate(20); 
        foreach($notices as &$notice) {
            if($notice->module == Module::Social) {
                $notice->module_content = Social::where('id', $notice->module_id)->with('fan')->first(); 
            }
        }

        return response()->json(['status' => 'success', 'data' => $notices]);  
    }
}
