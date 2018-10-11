<?php

namespace App\Http\Controllers\Api\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    
    public function store()
    {
        $username = request('username');
        $password = bcrypt(request('password'));
        $user = User::where('username',$username)->get();
        if(count($user)>0){
            return response()->json(['status' => 'error', 'msg' => '用户已存在']);
        }
        DB::beginTransaction();
        try {
            $user = User::create(['username' => $username, 'password' => $password]);
            DB::commit();
            return response()->json(['status' => 'success', 'msg' => '添加成功']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败：'.$e]);
        }
    }
}
