<?php

namespace App\Http\Controllers\Api\TodayLikes;

use App\Models\TodayLike;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TodayLikeController extends Controller
{
    //
    public function index()
    {
        $list = request(['today_id']);
        $tID = $list['today_id'];
        $data = TodayLike::where('today_id',$tID)->withCount('fans')->with('fan')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function isFanLike()
    {
        $list = request(['today_id']);
        $tID = $list['today_id'];
        $fanID = Token::getUid();
        $data = TodayLike::where([['today_id',$tID],['fan_id',$fanID]])->get();
        $count = count($data);
        return response()->json(['status' => 'success', 'data' => $count]);
    }

    public function store()
    {
        $list = request(['today_id']);
        $fanID = Token::getUid();
        $list['fan_id'] = $fanID;
        DB::beginTransaction();
        try {
            TodayLike::create($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function delete()
    {
        $list = request(['today_id']);
        $tID = $list['today_id'];
        $fanID = Token::getUid();
        DB::beginTransaction();
        try {
            TodayLike::where([['today_id',$tID],['fan_id',$fanID]])->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '删除失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }
}
