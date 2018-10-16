<?php

namespace App\Http\Controllers\Api\Specials;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use App\Models\Special;

class SpecialController extends Controller
{
    //
    public function index()
    {
        $data = Special::all();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function show()
    {
        $id = request()->special;
        $data = Special::find($id);
        $imgIDS  = $data->img_id;
        $imgs = Picture::whereIn('id',$imgIDS)->get();
        $data->imgs = $imgs;
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function store()
    {
        $list = request(['title', 'img_id','text', 'switch']);
        DB::beginTransaction();
        try {
            Special::create($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function update()
    {
        $list = request(['title', 'img_id', 'text', 'switch']);
        $id = request()->special;
        DB::beginTransaction();
        try {
            Special::where('id', $id)->update($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败！' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function destroy()
    {
        $id = request()->special;
        DB::beginTransaction();
        try {
            Special::where('id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '删除失败！' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '删除成功！']);
    }



}
