<?php

namespace App\Http\Controllers\Api\Specials;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use App\Models\Special;
use Illuminate\Support\Facades\DB;


class SpecialController extends Controller
{
    //
    public function index()
    {
        $data = Special::paginate(20);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function show()
    {
        $id = request()->special;
        $data = Special::find($id);
        $imgIDS  = json_decode($data->img_id,true);
        $imgs = Picture::whereIn('id',$imgIDS)->get();
        $data->imgs = $imgs;
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function store()
    {
        $list = request(['title', 'img_id','text', 'switch']);
        $list['img_id']=json_encode($list['img_id'],JSON_UNESCAPED_SLASHES);
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
        $list['img_id']=json_encode($list['img_id'],JSON_UNESCAPED_SLASHES);
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

    public function updateSwitch()
    {
        $list = request(['id', 'switch']);
        DB::beginTransaction();
        try {
            Special::where('id', $list['id'])->update(['switch'=>$list['switch']]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败！' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

//    public function miniIndex()
//    {
//        $comments = SpecialComment::where('special_id',request('special'))->with(['fan', 'toFan'])->orderBy('created_at', 'asc')->get();
//        $comments = Common::getCommentTree($comments->toArray());
//        return response()->json(['status' => 'success', 'data' => $comments]);
//    }


}
