<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Fan;
use App\Services\Token;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class FanController extends Controller
{
    public function getToken()
    {
        $config =  [
            'app_id' => config('wechat.mini_program.default.app_id'),
            'secret' => config('wechat.mini_program.default.secret'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => config('wechat.defaults.log.file'),
            ],
        ];
        
        $app = Factory::miniProgram($config);

        $user = $app->auth->session(request('code'));

        $miniToken = new \App\Services\MiniProgramToken();
        
        $token = $miniToken->getToken($user);

        return response()->json(['token' => $token]);
    }

    public function saveInfo() 
    {
        $token = request()->header('token');
        $data = Cache::get($token);
        $data = json_decode($data, true);
        $userInfo = request('userInfo');
        $userInfo['nickname'] = $userInfo['nickName'];
        $userInfo['status'] = 1;
        unset($userInfo['nickName']);

        if(Fan::where('id', $data['uid'])->update($userInfo)){
            return response()->json('保存成功');
        }

        return response()->json('保存失败'); 
    }

    public function verifyToken() 
    {
        return response()->json(['isValid' => Token::verifyToken(request()->header('token'))]);
    }
}
