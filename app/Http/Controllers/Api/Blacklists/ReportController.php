<?php

namespace App\Http\Controllers\Api\Blacklists;

use App\Http\Requests\ReportRequest;
use App\Http\Requests\ShareRequest;
use App\Models\Report;
use App\Models\Sign;
use App\Models\Social;
use App\Models\SocialComment;
use App\Models\SpecialComment;
use App\Services\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function show()
    {
        $datas=Report::with('report_fan:id,nickname')
            ->with('bereport_fan:id,nickname');
        $status=request('status');
        if(isset($status)){
            $datas=$datas->where('status',request('status'));
        }
        $datas=$datas->orderBy('status','asc')
            ->orderBy('created_at','desc')->paginate(20);
        foreach ($datas as $data){
            switch ($data->type){
                case 'socials':
                    $data->content=$data->comment_s;
                    unset($data->comment_s);
                    break;
                case 'social_comments':
                    $data->content=$data->comment_sc;
                    unset($data->comment_sc);
                    break;
                case 'special_comments':
                    $data->content=$data->comment_sp;
                    unset($data->comment_sp);
                    break;
            }
        }
        return response()->json(['status' => 'success', 'data' => $datas]);
    }
    public function store(ReportRequest $request)
    {
        $data = request()->all();
        $data['reporter_id']=Token::getUid();
        if(Report::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function verify()
    {
        $report_id=request()->report_id;
        $report_data=Report::find($report_id);
        if(request()->verify=='1'){
            switch ($report_data->type){
                case 'socials':
                    $model=Social::where('id',$report_data->comment)
                        ->update(['hidden'=>'1']);
                    break;
                case 'social_comments':
                    $model=SocialComment::where('id',$report_data->comment)
                        ->update(['hidden'=>'1']);
                    break;
                case 'special_comments':
                    $model=SpecialComment::where('id',$report_data->comment)
                        ->update(['hidden'=>'1']);
                    break;
            }
        }
        $updata=Report::where('id',$report_id)->update(['status'=>'1']);
        if($updata) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }


}
