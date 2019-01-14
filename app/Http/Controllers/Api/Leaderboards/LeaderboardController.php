<?php

namespace App\Http\Controllers\Api\Leaderboards;

use App\Models\Leaderboard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    //
    public function show()
    {
        $id = request()->leaderboard;
        $date = Leaderboard::where('id', $id)->with('picture')->first();
        return response()->json(['data' => $date]);
    }

    public function store()
    {
        $list = request(['ids', 'date_id']);
        DB::beginTransaction();
        try {
            $data = [];
            foreach ($list['ids'] as $id) {
                $leaderboard = Leaderboard::create(['img_id' => $id, 'date_id' => $list['date_id']]);
//                $data[] = $leaderboard;
                array_push($data,$leaderboard);
            }
            DB::commit();
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }

    }

    public function update()
    {
        $id = request()->leaderboard;
        $list = request(['ranking', 'old_ranking', 'up', 'is_first', 'is_hidden','count','definition','sid']);
        DB::beginTransaction();
        try {
            Leaderboard::where('id', $id)->update($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
    }

    public function destroy()
    {
        $id = request()->leaderboard;
        Leaderboard::where('id', $id)->delete();
    }

    public function getData()
    {
        $date_id = request('id');
        $data = Leaderboard::where([['date_id',$date_id],['is_hidden',0],['sid',0]])
            ->with('allChildrens')
            ->get();
        return response()->json(['data' =>$data]);
    }

    public function isHidden()
    {
        $date_id = request('date_id');
        Leaderboard::where('date_id',$date_id)
            ->update(['is_hidden'=>0]);

    }

    public function getDataByID()
    {
        $id = request()->leaderboard;
        $data = Leaderboard::where([['id',$id],['is_hidden',0],['sid',0]])
            ->orWhere([['sid',$id],['is_hidden',0]])
            ->get();
        return response()->json(['data' =>$data]);
    }

}
