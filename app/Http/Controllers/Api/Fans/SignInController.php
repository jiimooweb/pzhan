<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Fan;
use App\Models\PointHistory;
use App\Models\Sign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignInController extends Controller
{

    public function show()
    {
        $fan_id=request('fan_id');
        $sign=Sign::where('fan_id',$fan_id)->first();
        return response()->json(['status' => 'success', 'data' => $sign]);
    }

    public function signIn()
    {
        $point=config('common.sign_point');
        $con_point=config('common.sign_continuity');
        $data['fan_id']=Token::getUid();
        $fan_data=Fan::find($data['fan_id']);
        $data['last_day']=$datetime = Carbon::now()->toDateString();
        $data['continuity']=0;
        $data['continuity_day']=1;
        $sign_data=Sign::where('fan_id',$data['fan_id'])->first();
//        if($sign_data){
        $date=Carbon::parse($sign_data->last_day)->modify('+1 days')->toDateString();
        if($date==$data['last_day']){
            $data['continuity']=1;
            $data['continuity_day']=$sign_data->continuity_day+1;
            $point=$fan_data->point+$con_point;
            $add_point=$con_point;
        }else{
            $point=$fan_data->point+$point;
            $add_point=$point;
        }
        $save=Sign::where('fan_id',$data['fan_id'])->update($data);
//        }
//        else{
//            $save=Sign::create($data);
//            $point=$fan_data->point+$point;
//        }
        if($save){
            $fan_data->update(['point'=>$point]);
            PointHistory::create(['fan_id'=>$data['fan_id'],'state'=>'1','point'=>$add_point,
                'tag'=>'signIn','comment'=>$data['last_day'].'签到所得']);
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function signInHistory()
    {
        $year=request('year');
        $month=request('month');
        $fan_id=request('fan_id');
        $one_day=Carbon::create($year,$month,1);
        $last_day=Carbon::create($year,$month+1,0);
        $star_week=$one_day->dayOfWeek;
        $month_day=$last_day->diffInDays($one_day)+$star_week;
        $signs=PointHistory::where('fan_id',$fan_id)->where('tag','signIn')
            ->where('comment','like',$year."-".$month."%")->get();
        for ($i=0;$i<=$month_day;$i++){
            if($i<$star_week){
                $date_array[$i]=[];
            }else{
                $date=Carbon::create($year,$month,$i-$star_week+1);
                $flag=false;
                foreach ($signs as $sign){
                    if($sign->comment==$date->toDateString()."签到所得"){
                        $flag=true;
                        break;
                    }
                }
                $date_array[$i]=['isToday'=>$date->format('Ymd'),
                    'dateNum'=>$date->day ,'flag'=>$flag];
            }
        }
        return response()->json(['status' => 'success', 'data' => $date_array]);
    }
}
