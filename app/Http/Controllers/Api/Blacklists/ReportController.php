<?php

namespace App\Http\Controllers\Api\Fans;

use App\Http\Requests\ReportRequest;
use App\Http\Requests\ShareRequest;
use App\Models\Report;
use App\Models\Sign;
use App\Models\Social;
use App\Models\SocialComment;
use App\Services\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function show()
    {
        $data=Report::with('report_fan')->with('bereport_fan')->with('cause')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    public function store(ReportRequest $request)
    {
        $data = request()->all();
        $data['	reporter_id']=Token::getUid();
        if(ReportCause::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function verify()
    {
        $report_id=request()->report;
        $report_data=Report::find($report_id);
        if(request()->verify=='1'){
            switch ($report_data->type){
                case 'social':
                    $model=Social::class;
                    break;
                case 'social_comments':
                    $model=SocialComment::class;
                    break;
            }
            $social_updata=$model->where('id',$report_data->comments)
                ->update(['hidden','1']);
        }
        $updata=Report::where('id',$report_id)->updata(['status'=>'1']);
        if($updata) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }


}
