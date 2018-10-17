<?php

namespace App\Http\Controllers\Api\Fans;

use App\Http\Requests\ShareRequest;
use App\Models\Sign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShareController extends Controller
{

    public function share(ShareRequest $request){
        $add_point=mt_rand(config('common.share_point_min'),config('common.share_point_max'));
        $fan_id=request('fan_id');
        $friend_id=request('friend_id');
        $fan_data=Fan::find($fan_id);
        $friend_data=Fan;;find($friend_id);
        $point=$fan_data->point+$add_point;
        if($fan_data->update(['point'=>$point])) {
            PointHistory::create(['fan_id'=>$fan_id,'state'=>'1','point'=>$add_point,
                'tag'=>'share','comment'=>'分享'.$friend_data->nickname.'关注所得']);
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

}
