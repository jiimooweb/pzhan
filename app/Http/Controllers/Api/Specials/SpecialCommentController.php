<?php

namespace App\Http\Controllers\APi\Specials;

use App\Models\CommentNotice;
use App\Models\SpecialComment;
use App\Services\Token;
use App\Utils\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecialCommentController extends Controller
{
    //
    public function getcomments()
    {
        $id = request()->special;
        $data = SpecialComment::where([['special_id',$id],['pid',0]])->with(['fan', 'toFan'])->withCount('replys')->orderBy('created_at', 'asc')->get();
        foreach($data as $key => &$comment) {
            if($key < 3) {
                $comment->replys = SpecialComment::where('pid',$comment->id)->with(['fan', 'toFan'])->orderBy('created_at', 'asc')->limit(2)->get();
            }else {
                $comment->replys = [];
            }
        }
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function comment()
    {
        $comment = request()->all();
        $comment = SpecialComment::create($comment);

        if($comment) {
            return response()->json(['status' => 'success', 'data' => $comment->where('id', $comment->id)->with(['replys','fan', 'toFan'])->withCount(['replys'])->first()]);
        }

        return response()->json(['status' => 'error']);

    }

    public function replys()
    {

        $comment = SpecialComment::where('id', request()->id)->with(['fan', 'toFan'])->withCount('replys')->orderBy('created_at', 'asc')->first();
        $replys = SpecialComment::where('pid', request()->id)->with(['fan', 'toFan'])->orderBy('created_at', 'asc')->get();

        return response()->json(['status' => 'success', 'comment' => $comment,'replys' => $replys]);

    }

    public function deleteComment()
    {
        $delete_count = 0;
        if(SpecialComment::where('id' ,request()->id)->delete()) {
            $delete_count = SpecialComment::where('pid', request()->id)->delete();
            return response()->json(['status' => 'success', 'count' => $delete_count + 1]);
        }

        return response()->json(['status' => 'error']);
    }

    public function addCommentNotice()
    {
        $notice_fans = [];
        $data = request()->all();
        $fan_id = request('fan_id') ?? Token::getUid();

        if($data['to_fan_id'] > 0 && $data['to_fan_id'] != $fan_id) {
            array_push($notice_fans, $data['to_fan_id']);
        }

        $notice_fans = array_unique($notice_fans);

        if($notice_fans) {
            foreach($notice_fans as $notice_fan) {
                $notice = [
                    'fan_id' => $notice_fan,
                    'from_fan_id' => $fan_id,
                    'to_fan_id' => $data['to_fan_id'],
                    'content' => $data['content'],
                    'module_id' => $data['module_id'],
                    'module' => Module::Special,
                ];
                CommentNotice::create($notice);
            }
        }
        return response()->json(['status' => 'success']);
    }

    public function addReplyNotice() {
        $notice_fans = [];
        $data = request()->all();
        $fan_id = request('fan_id') ?? Token::getUid();
        $comment_fan_id = request('comment_fan_id');


        if($fan_id != $comment_fan_id) {
            array_push($notice_fans, $comment_fan_id);
        }

        if($data['to_fan_id'] > 0 && $data['to_fan_id'] != $fan_id) {
            array_push($notice_fans, $data['to_fan_id']);
        }

        $notice_fans = array_unique($notice_fans);

        if($notice_fans) {
            foreach($notice_fans as $notice_fan) {
                $notice = [
                    'fan_id' => $notice_fan,
                    'from_fan_id' => $fan_id,
                    'to_fan_id' => $data['to_fan_id'],
                    'content' => $data['content'],
                    'module_id' => $data['module_id'],
                    'module' => Module::Social,
                ];
                CommentNotice::create($notice);
            }
        }

        return response()->json(['status' => 'success']);
    }



}
