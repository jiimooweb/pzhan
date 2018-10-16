<?php

namespace App\Http\Controllers\Api\Todays;

use App\Models\Today;
use App\Services\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TodayController extends Controller
{
    public function store()
    {
        $list = request(['title', 'img_id', 'pid', 'text', 'date']);
        DB::beginTransaction();
        try {
            Today::create($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function search()
    {
        $date = request('date');
        $data = Today::where('date', $date)->with('picture')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function update()
    {
        $list = request(['title', 'img_id', 'pid', 'text', 'date']);
        $id = request()->today;
        DB::beginTransaction();
        try {
            Today::where('id', $id)->update($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败！' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function delete()
    {
        $list = request('ids');
        DB::beginTransaction();
        try {
            Today::destroy($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '删除失败！' . $e]);
        }
        return response()->json(['status' => 'error', 'msg' => '删除成功！' . $e]);
    }

    public function miniIndex()
    {
//        缺评论
        $list = request(['date']);
        $date = $list['date'];
        $fanID = Token::getUid();
        $data = Today::where('date', $date)->withCount('todayLikes')
            ->withCount(['todayLikes as isLike' => function ($query) use($fanID) {
                    $query->where('fan_ID',$fanID);
            }])->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
