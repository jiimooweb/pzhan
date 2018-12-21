<?php

namespace App\Http\Controllers\Api\Leaderboards;

use App\Models\Leaderboard;
use App\Models\LeaderDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderDateController extends Controller
{
    //
    public function getDate()
    {
        $list = request(['year', 'month']);
        $data = LeaderDate::whereYear('date', $list['year'])
            ->whereMonth('date', $list['month'])
            ->get();
        return response()->json(['data' => $data]);
    }

    public function show()
    {
        $id = request()->leaderDate;
        $data = LeaderDate::where('id', $id)
            ->with(['leaderboards' => function ($query) {
                $query->orderBy('ranking')->with('picture');
            }])
            ->first();
        return response()->json(['data' =>$data]);
    }

    public function store()
    {
        $date = request('date');
        LeaderDate::firstOrCreate(['date' => $date]);
    }

    public function update()
    {
        $id = request()->leaderDate();
        $date = request('date');
        LeaderDate::where('id', $id)->update(['date' => $date]);
    }

    public function destroy()
    {
        $id = request()->leaderDate;
        LeaderDate::where('id', $id)->delete();
        DB::beginTransaction();
        try {
            LeaderDate::where('id', $id)->delete();
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

    public function getDateforSP()
    {
//        $today = Carbon::today();
        $today = Carbon::parse('2019-04-12');
        $year = $today->year;
        $month = $today->month;
        $day = $today->day;
        $monthf = $this->monthFormat($month);
        $list = [];
        for($i = 2018;$i<$year+1;$i++){
            if($i == 2018){
                array_push($list,['year'=>2018,'month'=>[12]]);
            }else if( $i !=2018 ){
                if($i == $year){
                    for($r=$month;$r>=1;$r--){
                        $arrMonth[] = $r;
                    }
                    array_push($list,['year'=>$i,'month'=>$arrMonth]);
                }else{
                    array_push($list,['year'=>$i,'month'=>[12,11,10,9,8,7,6,5,4,3,2,1]]);
                }
            }
        }
        $date['year']=$year;
        $date['month']=$month;
        $date['monthF']=$monthf;
        $date['day']=$day;
        $date['date'] = $today->toDateString();
        return response()->json(['data' => $list,'today'=>$date]);

    }
    public function getDataByDate()
    {
        $list = request(['year','month','day']);
        $date = Carbon::parse($list['year']."-".$list['month']."-".$list['day']);
        $data = LeaderDate::where('date',$date)
            ->with(['leaderboards' => function ($query) {
                $query->where([['sid',0],['is_hidden',0]])
                    ->with('allChildrens')
                    ->orderBy('ranking')
                    ->with('picture');
            }])
            ->first();
        if($data){
            $leaderboards = $data->leaderboards;
            $first = $leaderboards->filter(function ($item) {
                return $item->is_first == true ;
            })->all();
            return response()->json(['data' => $leaderboards,'first'=>$first]);
        }else{
            $today = Carbon::today();
            if($today->eq($date)){
                $yesterday = LeaderDate::where('date',$today->modify('-1 days'))
                    ->with(['leaderboards' => function ($query) {
                        $query->where([['sid',0],['is_hidden',0]])
                            ->with('allChildrens')
                            ->orderBy('ranking')
                            ->with('picture');
                    }])
                    ->first();
                if($yesterday){
                    $y_leaderboards = $yesterday->leaderboards;
                    $y_first = $y_leaderboards->filter(function ($item) {
                        return $item->is_first == true ;
                    })->all();
                    return response()->json(['data' => $y_leaderboards,'first'=>$y_first]);
                }
            }
            return response()->json(['data' => '','first'=>'']);
        }

    }

}
