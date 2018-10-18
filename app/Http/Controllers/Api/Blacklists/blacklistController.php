<?php

namespace App\Http\Controllers\Api\Blacklists;

use App\Models\Blacklist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class blacklistController extends Controller
{
    //
    public  function banList()
    {
        $state = request('state');
        $data = Blacklist::where([['state',$state],['is_seal',0]])->with('fan')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public  function sealList()
    {
        $data = Blacklist::where('is_seal',1)->with('fan')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function store()
    {
        $list = request(['fan_id','day','reason','is_seal']);
        $list['state'] =  1;
        DB::beginTransaction();
        try {
            Blacklist::create($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }


    public function update()
    {
        $list = request(['fan_id','day','state','reason','is_seal']);
        $id = request()->special;
        DB::beginTransaction();
        try {
            Blacklist::where('id', $id)->update($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function destroy()
    {
        $id = request()->blacklist;
        DB::beginTransaction();
        try {
            Blacklist::where('id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '删除失败！' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }
}
