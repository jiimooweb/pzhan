<?php

namespace App\Http\Controllers\APi\Specials;

use App\Models\SpecialComment;
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



}
