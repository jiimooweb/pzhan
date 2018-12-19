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
        return response()->json([$date]);
    }

    public function store()
    {
        $list = request(['ids', 'date_id']);
        DB::beginTransaction();
        try {
            foreach ($list['ids'] as $id) {
                Leaderboard::create(['img_id' => $id, 'date_id' => $list['date_id']]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }

    }

    public function update()
    {
        $id = request()->leaderboard;
        $list = request(['ranking', 'old_ranking', 'up', 'is_first', 'is_hidden']);
        DB::beginTransaction();
        try {
            Leaderboard::where('id', $id)->update($list);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
    }

    public function destroy()
    {
        $id = request()->leaderboard;
        LeaderDate::where('id', $id)->delete();
    }
}
