<?php

namespace App\Http\Controllers\Api\Leaderboards;

use App\Models\LeaderDate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderDateController extends Controller
{
    //
    public function getDate()
    {
        $list = request(['year','month']);
        $data = LeaderDate::whereYear('date',$list('year'))
            ->whereMonth('date',$list['month'])
            ->get();
        return response()->json([$data]);
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
    }

}
