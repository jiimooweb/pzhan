<?php

namespace App\Http\Controllers\Api\ThirdPartyAds;

use App\Models\Fan;
use App\Models\ThirdPartyAd;
use App\Services\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ThirdPartyAdController extends Controller
{
    //
    public function switch()
    {
        $type = request('type');
        $title = "加分";
        $switch_title = 0;
        $fan_id = Token::getUid();
        $today = Carbon::today();
        $ads = ThirdPartyAd::where([['fan_id', $fan_id], ['type', $type]])
            ->whereDate('created_at', '>=', $today->toFormattedDateString())
            ->get();
        $count = count($ads);
        if ($count == 0) {
            $switch_title = 1;
        } else if ($count == 1) {
            $record = Carbon::parse($ads[0]->created_at);
            if ($record->lt($start) && $now->gte($start)) {
                $switch_title = 1;
            }
        }
        if($type==0){
//            主页
            $switch_ad =1;
        }else{
//            预览
            $switch_ad = 1;
        }
        return response()->json(['tSwitch' => $switch_title, 'adSwitch'=>$switch_ad,'title' => $title]);
    }

    public function add()
    {
        $type = request('type');
        $fan_id = Token::getUid();
        $today = Carbon::today();
        $start = Carbon::today()->addHours(12);
        $integral = rand(10, 20);
        $now = Carbon::now();
        $ads = ThirdPartyAd::where([['fan_id', $fan_id], ['type', $type]])
            ->whereDate('created_at', '>=', $today->toFormattedDateString())
            ->get();
        $count = count($ads);
        if ($count == 0) {
            DB::beginTransaction();
            try {
                ThirdPartyAd::create(['fan_id' => $fan_id,'integral' => $integral,'type' => $type]);
                Fan::where('id', $fan_id)->increment('point', $integral);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        } else if ($count == 1) {
            $record = Carbon::parse($ads[0]->created_at);
            if ($record->lt($start) && $now->gte($start)) {
                DB::beginTransaction();
                try {
                    ThirdPartyAd::create(['fan_id' => $fan_id], ['integral' => $integral], ['type' => $type]);
                    Fan::where('id', $fan_id)->increment('point', $integral);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                }
            }

        }
        return response()->json(['tSwitch' => 0]);
    }
}
