<?php

namespace App\Http\Controllers\Api\Fans;

use App\Http\Requests\SignRequest;
use App\Models\Sign;
use App\Models\SignHistory;
use App\Models\Social;
use App\Services\Qiniu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignController extends Controller
{
    public function index() 
    {

    }

    public function store(SignRequest $request)
    {
        $data['fan_id']=request('fan_id');
        $data['last_day']=$datetime = Carbon::now();
        $data['sign_point']=config('common.sign_point');
        if(Sign::create($data)){
            SignHistory::create(['fan_id'=>$data['fan_id'],'sign_day'=>$data['last_day']]);
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function show()
    {

    }

    public function update(SignRequest $request)
    {
        $point=config('common.sign_point');
        $con_point=config('common.sign_continuity');
        $date['fan_id']=request('fan_id');
        $data['last_day']=$datetime = Carbon::now();
        $sign_data=Sign::where('fan_id',$date['fan_id'])->first();
        $date=$sign_data->last_day->modify('+1 days');
        if($date->eq($data['last_day'])){
            $date['continuity']=1;
            $date['continuity_day']=$sign_data->continuity_day++;
            $date['sign_point']=$sign_data->sign_point+$con_point;
        }else{
            $date['continuity']=0;
            $date['continuity_day']=1;
            $date['sign_point']=$sign_data->sign_point+$point;
        }
        if(Sign::where('id', $date['fan_id'])->update($data)) {
            SignHistory::create(['fan_id'=>$data['fan_id'],'sign_day'=>$data['last_day']]);
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {

    }



}
