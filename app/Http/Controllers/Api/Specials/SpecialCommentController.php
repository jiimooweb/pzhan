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

}
