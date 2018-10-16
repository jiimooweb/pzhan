<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Sign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignInController extends Controller
{


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
            }else{
                $point=$fan_data->point+$point;
            }
            $save=Sign::where('fan_id',$data['fan_id'])->update($data);
//        }
//        else{
//            $save=Sign::create($data);
//            $point=$fan_data->point+$point;
//        }
        if($save){
            $fan_data->update(['point'=>$point]);
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }
}
