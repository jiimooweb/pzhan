<?php

namespace App\Http\Controllers\Api\Fans;

use App\Http\Requests\ShareRequest;
use App\Models\PointHistory;
use App\Models\ShareHistory;
use App\Models\Sign;
use App\Services\Token;
use App\Models\Fan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShareController extends Controller
{

    public function showShare(){
        $fan_id=request('fan_id');
        $share_data=ShareHistory::where('share_id',$fan_id)
            ->with(['share_fan:id,nickname,avatarUrl'])
            ->with(['beshare_fan::id,nickname,avatarUrl'])
            ->orderBy('created_at','desc')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $share_data]);
    }

    public function share(ShareRequest $request){
        $add_point=mt_rand(config('common.share_point_min'),config('common.share_point_max'));
        $fan_id=request('fan_id');
        $friend_id=Token::getUid();
        $fan_data=Fan::find($fan_id);
        $friend_data=Fan::find($friend_id);
        $share_histories=ShareHistory::where('share_id',$fan_id)->
        where('beshare_id',$friend_id)->first();
        if($share_histories){
            return response()->json(['status' => 'repeat', 'msg' => '您已给该好友助力过了']);
        }
        $point=$fan_data->point+$add_point;
        if($fan_data->update(['point'=>$point])) {
            PointHistory::create(['fan_id'=>$fan_id,'state'=>'1','point'=>$add_point,
                'tag'=>'share','comment'=>'分享'.$friend_data->nickname.'关注得分']);
            ShareHistory::create(['share_id'=>$fan_id,'beshare_id'=>$friend_id,
                'reward'=>$add_point,'reward_type'=>'point']);
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

}
