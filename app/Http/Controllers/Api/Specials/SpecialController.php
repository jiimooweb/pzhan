<?php

namespace App\Http\Controllers\Api\Specials;

use App\Models\Picture;
use App\Models\Special;
use App\Services\Token;
use App\Models\SpecialImg;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class SpecialController extends Controller
{
    //web
    public function index()
    {
        $data = Special::orderBy('created_at','desc')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function show()
    {
        $id = request()->special;
        $data =Special::where('id',$id)->with('imgs')->first();
        return response()->json(['status' => 'success', 'data' => $data]);

    }

    public function store()
    {
        $list = request(['title','text','switch','cover']);
        $img_id = request('img_id');
//        $list['img_id']=json_encode($list['img_id'],JSON_UNESCAPED_SLASHES);
        $special=[];

        DB::beginTransaction();
        try {
            $data = Special::create($list);
            $id = $data->id;
            foreach ($img_id as $item){
                $special['img_id'] = $item ;
                $special['special_id'] =$id;
                SpecialImg::create($special);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);

    }

    public function update()
    {
        $list = request(['title', 'text', 'switch','cover']);
        $img_id = request('img_id');
//        $list['img_id']=json_encode($list['img_id'],JSON_UNESCAPED_SLASHES);
        $id = request()->special;
        DB::beginTransaction();
        try {
            Special::where('id', $id)->update($list);
            SpecialImg::where('special_id',$id)->delete();
            foreach ($img_id as $item){
                $special['img_id'] = $item ;
                $special['special_id'] =$id;
                SpecialImg::create($special);
            }
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


    public function miniIndex()
    {
        $data = Special::where('switch',1)->orderBy('created_at','desc')->paginate(10);
        return response()->json(['data' => $data]);
    }

    public function getRes()
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $id = request('id');

        DB::transaction(function () use($id){
           Special::where('id',$id)->increment('visits');
        });

        $data = Special::where([['id',$id],['switch',1]])
            ->with(['imgs'=>function($query){
                $query->with('tags');
            }])
            ->first();
        foreach($data->imgs as &$img) {
            $img->collect = $img->isCollect($fan_id) ? 1 : 0;
        }

        return response()->json(['data' => $data]);
    }

    public function doSearch()
    {
        $key = request('key');
        $data = Special::where([['title','like','%'.$key.'%'],['switch',1]])->paginate(18);
        return response()->json(['data' => $data]);
    }

    public function getHot() {
        $data = Special::where('switch',1)->orderBy('created_at','desc')->limit(2)->get();
        return response()->json(['data' => $data]);
    }

}
