<?php

namespace App\Http\Controllers\Api\Leaderboards;

use App\Models\Leaderboard;
use App\Models\LeaderDate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderDateController extends Controller
{
    //
    public function getDate()
    {
        $list = request(['year','month']);
        $data = LeaderDate::whereYear('date',$list['year'])
            ->whereMonth('date',$list['month'])
            ->get();
        return response()->json(['date'=>$data]);
    }

    public function show()
    {
        $id = request()->leaderDate;
        $data = LeaderDate::where('id',$id)
            ->with(['leaderboards' => function ($query) {
                $query->with('picture');
            }])
            ->first();
        return response()->json([$data]);
    }

    public function store()
    {
        $date = request('date');
        LeaderDate::create(['date'=>$date]);
    }

    public function update()
    {
        $id = request()->leaderDate();
        $date = request('date');
        LeaderDate::where('id',$id)->update(['date'=>$date]);
    }

    public function destroy()
    {
        $id = request()->leaderDate;
        LeaderDate::where('id',$id)->delete();
        DB::beginTransaction();
        try {
            LeaderDate::where('id',$id)->delete();
            Leaderboard::where('date_id', $id)->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
    }


    public function monthFormat($month)
    {
        switch ($month) {
            case 1:
                $value = 'Jan.';
                break;
            case 2:
                $value = 'Feb.';
                break;
            case 3:
                $value = 'Mar.';
                break;
            case 4:
                $value = 'Apr.';
                break;
            case 5:
                $value = 'May.';
                break;
            case 6:
                $value = 'June.';
                break;
            case 7:
                $value = 'July.';
                break;
            case 8:
                $value = 'Aug.';
                break;
            case 9:
                $value = 'Sept.';
                break;
            case 10:
                $value = 'Oct.';
                break;
            case 11:
                $value = 'Nov.';
                break;
            default:
                $value = 'Dec';

        }
        return $value;
    }


}
