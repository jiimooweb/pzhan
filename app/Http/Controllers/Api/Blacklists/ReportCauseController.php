<?php

namespace App\Http\Controllers\Api\Blacklists;

use App\Http\Requests\ReportCauseRequest;
use App\Http\Requests\ShareRequest;
use App\Models\ReportCause;
use App\Models\Sign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportCauseController extends Controller
{
    public function index()
    {
        $report_cause=ReportCause::all();
        return response()->json(['status' => 'success', 'data' => $report_cause]);
    }

    public function store(ReportCauseRequest $request)
    {
        $data = request()->all();
        if(ReportCause::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update(ReportCauseRequest $request)
    {
        $data = request()->all();
        if(ReportCause::where('id', request()->report_cause)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(ReportCause::where('id', request()->report_cause)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }
}
