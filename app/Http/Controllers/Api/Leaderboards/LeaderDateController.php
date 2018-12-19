<?php

namespace App\Http\Controllers\Api\Leaderboards;

use App\Models\LeaderDate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderDateController extends Controller
{
    //
    public function index()
    {
        $date = LeaderDate::orderBy('created_at','desc')->paginate('20');
        return response()->json([$date]);
    }

    public function show()
    {
        $id = request()->leaderDate;
        $date = LeaderDate::where('id',$id)
            ->with(['leaderboards' => function ($query) {
                $query->with('picture');
            }])
            ->first();
        return response()->json([$date]);
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
