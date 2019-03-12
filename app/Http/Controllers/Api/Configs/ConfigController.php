<?php

namespace App\Http\Controllers\Api\Configs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    //
    public function index()
    {
        $data = [];
        $data['me_reward_switch'] = 1 ; //我的 - 打赏支持
        $data['index_img_switch'] = 1 ; //首页 图片开关 暂时用于打赏
        $data['index_img'] = ""; //首页图片url
        $data['index_url'] = 0; // 首页图片跳转链接 为0默认跳转打赏小程序
        return response()->json(['status' => 'success', 'data' => $data]);

    }
}
