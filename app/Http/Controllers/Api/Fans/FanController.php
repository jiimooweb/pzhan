<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Fan;
use App\Models\Sign;
use App\Models\Picture;
use App\Services\Token;
use EasyWeChat\Factory;
use App\Models\FanShare;
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

        if(strlen($user['openid']) !== 28) {
            return response()->json(['msg' => $user]);
        }

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

    public function fans() {
        $status = request('status');
        $fans = Fan::when($status > -1, function($query) use ($status) {
            return $query->where('status', $status);
        })->paginate(30);
        return response()->json(['status' => 'success','data' => $fans]);
    }

    public function collect(Fan $fan)
    {
        $page = request('page');
        $limit = 15;        
        $offset = ($page - 1) * $limit;
        $picture_ids = $fan->collcetPictures->pluck('id')->toArray();
        $total = count($picture_ids);
        $picture_ids = array_slice($picture_ids, $offset, $limit);
        $pictures = [];
        $picture = new Picture();
        foreach($picture_ids as $picture_id) {
            $picture = $picture->where('id', $picture_id)->first();
            $picture->collect = 1;
            $pictures[] = $picture;
        }
        return response()->json(['status' => 'success','data' => $pictures, 'total' => $total]);
    }

    public function download(Fan $fan)
    {
        $page = request('page');
        $limit = 15;        
        $offset = ($page - 1) * $limit;
        $picture_ids = $fan->downloadPictures->pluck('id')->toArray();
        $total = count($picture_ids);
        $picture_ids = array_slice($picture_ids, $offset, $limit);
        $pictures = [];
        $picture = new Picture();
        foreach($picture_ids as $picture_id) {
            $picture = $picture->where('id', $picture_id)->first();
            $picture->collect = 1;
            $pictures[] = $picture;
        }
        return response()->json(['status' => 'success','data' => $pictures, 'total' => $total]);
    }

    public function like(Fan $fan)
    {
        $picture_ids = $fan->likePictures->pluck('id');
        $pictures = Picture::with(['tags'])->when($picture_ids, function($query) use ($picture_ids) {
            return $query->whereIn('id', $picture_ids);
        })->withCount(['likeFans', 'collectFans'])->orderBy('created_at', 'desc')->paginate(15); 
        return response()->json(['status' => 'success','data' => $pictures]);
    }

    public function fanPicture()
    {
        $fan_id = request('fan_id') ?? Token::getUid();         
        $fan = Fan::where('id', $fan_id)->first();       
        $collect_ids = $fan->collcetPictures->pluck('id');
        $like_ids = $fan->likePictures->pluck('id');
        return response()->json(['status' => 'success','collect_ids' => $collect_ids,'like_ids' => $like_ids]);
    }

    public function getPointAndShareCount()
    {
        $fan_id = request('fan_id') ?? Token::getUid();     
        $fan = Fan::find($fan_id);
        $date = date('Y-m-d', time());
        $share_count = FanShare::whereDate('created_at', $date)->count();
        return response()->json(['status' => 'success','point' => $fan->point,'share_count' => 5 - $share_count]);
    }

    public function getUid() 
    {
        return response()->json(['uid' => Token::getUid()]);
    }

    public function getUserInfo() {
        $fan_id = request('fan_id') ?? Token::getUid();
        $fan  = Fan::find($fan_id );
        return response()->json(['status' => 'success','data' => $fan]);
    }
}
