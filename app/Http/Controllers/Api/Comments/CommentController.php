<?php

namespace App\Http\Controllers\Api\Comments;

use App\Models\Paramate;
use App\Models\SocialComment;
use App\Models\SpecialComment;
use App\Utils\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    //
    public function index()
    {
        $data = Paramate::where([['type','comment'],['switch',1]])->orderBy('created_at','desc')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function queryComments()
    {
        $list = request(['module','key']);
        $module = $list['module'];
        $key = $list['key'];
        $special = Module::Special;
        $social = Module::Social;
        switch ($module){
            case $special:
                $data = SpecialComment::where('content','like','%'.$key.'%')->with('fan')->with(['blacklists'=>function ($query){
                    $query->where('state',1)->orWhere('is_seal',1);
                }])->orderBy('created_at','desc')->paginate(20);

                $data->transform(function ($item,$key)use($special){
                     $item->module = $special;
                     return $item;
                });
                break;

            case $social:
                $data = SocialComment::where('content','like','%'.$key.'%')->with('fan')->with(['blacklists'=>function ($query){
                    $query->where('state',1)->orWhere('is_seal',1);
                }])->orderBy('created_at','desc')->paginate(20);

                $data->transform(function ($item,$key)use($social){
                    $item->module = $social;
                    return $item;
                });
                break;

            case 'all':
                $dataSpecial = SpecialComment::where('content','like','%'.$key.'%')->with('fan')->with(['blacklists'=>function ($query){
                    $query->where('state',1)->orWhere('is_seal',1);
                }])->orderBy('created_at','desc')->paginate(20);

                $dataSpecial->transform(function ($item,$key)use($special){
                    $item->module = $special;
                    return $item;
                });

                $dataSocial = SocialComment::where('content','like','%'.$key.'%')->with('fan')->with(['blacklists'=>function ($query){
                    $query->where('state',1)->orWhere('is_seal',1);
                }])->orderBy('created_at','desc')->paginate(20);

                $dataSocial->transform(function ($item,$key)use($social){
                    $item->module = $social;
                    return $item;
                });

                $data = $dataSpecial->union($dataSocial);
                break;
        }
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function delete()
    {
        $special = Module::Special;
        $social = Module::Social;

        $list = request(['module','id']);
        switch ($list['module']){
            case $special:

                DB::beginTransaction();
                try {
                    SpecialComment::find($list['id'])->delete();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'msg' => $e]);
                }

                break;

            case $social:

                DB::beginTransaction();
                try {
                    SocialComment::find($list['id'])->delete();
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'msg' => $e]);
                }

                break;
        }
        return response()->json(['status' => 'sucess', 'msg' => '删除成功！']);
    }
}
