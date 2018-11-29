<?php

namespace App\Http\Controllers\Api\Todays;

use App\Models\Today;
use App\Http\Controllers\Controller;
use App\Services\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TodayController extends Controller
{

    public function index()
    {
        $data = Today::with('picture')->withCount('todayLikes')->orderBy('today_likes_count','created_at', 'DESC')->paginate(20);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function store()
    {
        $list = request(['title', 'img_id', 'text', 'date']);
        $list['date'] = Carbon::parse($list['date']);
        DB::beginTransaction();
        try {
            Today::create($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function search()
    {
        $date = Carbon::parse(request('date'))->toDateString();
        $data = Today::where('date', $date)->with('picture')->withCount('todayLikes')->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function update()
    {
        $list = request(['title', 'img_id', 'text', 'date']);
        $list['date'] = Carbon::parse($list['date']);
        $id = request()->today;
        DB::beginTransaction();
        try {
            Today::where('id', $id)->update($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

    public function delete()
    {
        $list = request('ids');
        DB::beginTransaction();
        try {
            Today::destroy($list);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => $e]);
        }
        return response()->json(['status' => 'sucess', 'msg' => '删除成功！']);
    }


    public function getToday()
    {
//        $today = Carbon::parse('2019-02-02');
        $today = Carbon::today();
        $year = $today->year;
        $month = $today->month;
        $day = $today->day;
        $monthf = $this->monthFormat($month);
        $years = [];
        $months = [];
        $fanID = Token::getUid();
        if ($year > 2018) {
            for ($i = $year; $i >= 2018; $i--) {
                array_push($years, $i);
            }
            for ($i = $month; $i > 0; $i--) {
                array_push($months, $i);
            }

        } else {
            array_push($years, 2018);
            for ($i = $month; $i > 10; $i--) {
                array_push($months, $i);
            }
        }

        $data = Today::where('date', $today)->withCount('todayLikes')
            ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                $query->where('fan_ID', $fanID);
            }])
            ->with(['picture'=>function($query){
                $query->with('tags');
            }])
            ->get();

        $date['year'] = $year;
        $date['month'] = $month;
        $date['day'] = $day;
        $date['monthF'] = $monthf;
        $date['today'] = $today->toDateString();
        $date['years'] = $years;
        $date['months'] = $months;
        return response()->json(['data' => $data, 'date' => $date]);
    }

    public function getOther()
    {
        $reDate = request('date');
        $caDate = Carbon::parse($reDate);
        $firstDay = Carbon::parse($reDate)->firstOfMonth();
        $lastDay = Carbon::parse($reDate)->lastOfMonth();
//        $today = Carbon::parse('2019-02-02');
        $today = Carbon::today();
        $fanID = Token::getUid();
        if ($caDate->month == $today->month) {
            $data = Today::whereBetween('date', [$firstDay, $today])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])
                ->with(['picture'=>function($query){
                    $query->with('tags');
                }])
                ->orderBy('date', 'desc')->get();
            $data->transform(function ($item) {
                $record = Carbon::parse($item->date);
                $item->day = $record->day;
                $item->month = $record->month;
                $item->year = $record->year;
                return $item;
            });
            $data = $data->groupBy('date');
            return response()->json(['data' => $data]);
        } else {
            $data = Today::whereBetween('date', [$firstDay, $lastDay])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])
                ->with(['picture'=>function($query){
                    $query->with('tags');
                }])
                ->orderBy('date', 'desc')->get();
            $data->transform(function ($item) {
                $record = Carbon::parse($item->date);
                $item->day = $record->day;
                $item->month = $record->month;
                $item->year = $record->year;
                return $item;
            });
            $data = $data->groupBy('date');
            return response()->json(['data' => $data]);
        }

    }

    public function getDataByYearMonth()
    {
        $list = request(['year', 'month']);
        $string = $list['year'] . '-' . $list['month'] . '-01';
        $firstDay = Carbon::parse($string);
        $lastDay = Carbon::parse($string)->lastOfMonth();
//        $today = Carbon::parse('2019-02-02');
        $today = Carbon::today();
        $fanID = Token::getUid();

        if ($today->month == $list['month'] && $today->year == $list['year']) {
            $data = Today::whereBetween('date', [$firstDay, $today])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])
                ->with(['picture'=>function($query){
                    $query->with('tags');
                }])
                ->orderBy('date', 'desc')->get();
            $data->transform(function ($item) {
                $record = Carbon::parse($item->date);
                $item->day = $record->day;
                $item->month = $record->month;
                $item->year = $record->year;
                return $item;
            });
            $data = $data->groupBy('date');
            return response()->json(['data' => $data]);
        } else {
            $data = Today::whereBetween('date', [$firstDay, $lastDay])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])
                ->with(['picture'=>function($query){
                    $query->with('tags');
                }])
                ->orderBy('date', 'desc')->get();
            $data->transform(function ($item) {
                $record = Carbon::parse($item->date);
                $item->day = $record->day;
                $item->month = $record->month;
                $item->year = $record->year;
                return $item;
            });
            $data = $data->groupBy('date');
            return response()->json(['data' => $data]);

        }
    }

    public function getDataByYear()
    {
        $reYear = request('year');
//        $today = Carbon::parse('2019-02-02');
        $today = Carbon::today();
        $fanID = Token::getUid();
        $month = $today->month;
        $months = [];

        if ($reYear > 2018) {
            for ($i = $month; $i > 0; $i--) {
                array_push($months, $i);
            }
            $date['day'] = $today->day;
            $date['month'] = $month;
            $date['months'] = $months;
            $date['monthF'] = $this->monthFormat($month);

            $firstDay = Carbon::today()->firstOfMonth();
            $data = Today::whereBetween('date', [$firstDay, $today])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])
                ->with(['picture'=>function($query){
                    $query->with('tags');
                }])
                ->orderBy('date', 'desc')->get();
            $data->transform(function ($item) {
                $record = Carbon::parse($item->date);
                $item->day = $record->day;
                $item->month = $record->month;
                $item->year = $record->year;
                return $item;
            });
            $record = $data[0]->date;
            $data = $data->groupBy('date');
            $date['data'] = $data[$record];
            return response()->json(['data' => $data, 'date' => $date]);
        } else {
            for ($i = 12; $i > 10; $i--) {
                array_push($months, $i);
            }
            $date['day'] = 31;
            $date['month'] = 12;
            $date['months'] = $months;
            $date['monthF'] = $this->monthFormat($month);

            $firstDay = Carbon::parse('2018-12-01');
            $lastDay = Carbon::parse('2018-12-31');
            $data = Today::whereBetween('date', [$firstDay, $lastDay])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])
                ->with(['picture'=>function($query){
                    $query->with('tags');
                }])
                ->orderBy('date', 'desc')->get();
            $data->transform(function ($item) {
                $record = Carbon::parse($item->date);
                $item->day = $record->day;
                $item->month = $record->month;
                $item->year = $record->year;
                return $item;
            });
            $record = $data[0]->date;
            $data = $data->groupBy('date');
            $date['data'] = $data[$record];
            return response()->json(['data' => $data, 'date' => $date]);
        }

    }

    public function getDate()
    {
        $rdate = Carbon::parse(request('date'));
        $rday = $rdate->day;
        $rmonth = $rdate->month;
        $ryear = $rdate->year;

        $today = Carbon::today();
        $year = $today->year;
        $month = $today->month;
        $day = $today->day;

        $years = [];
        $months = [];
        $rmonths = [];
        $fanID = Token::getUid();

        if ($year > 2018) {
            for ($i = $year; $i >= 2018; $i--) {
                array_push($years, $i);
            }
            for ($i = $month; $i > 0; $i--) {
                array_push($months, $i);
            }

            if($ryear==2019){
                for ($i = $month; $i > 0; $i--) {
                    array_push($rmonths, $i);
                }
            }else if($ryear==2018){
                for ($i = 12; $i > 10; $i--) {
                    array_push($rmonths, $i);
                }
            }
        } else {
            array_push($years, 2018);
            for ($i = $month; $i > 10; $i--) {
                array_push($rmonths, $i);
                array_push($months, $i);
            }
        }

        $data = Today::where('date', $rdate)->withCount('todayLikes')
            ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                $query->where('fan_ID', $fanID);
            }])
            ->with(['picture'=>function($query){
                $query->with('tags');
            }])
            ->get();

        $tdata = Today::where('date', $today)->withCount('todayLikes')
            ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                $query->where('fan_ID', $fanID);
            }])
            ->with(['picture'=>function($query){
                $query->with('tags');
            }])->get();

        $date['tyear'] = $year;
        $date['tmonth'] = $month;
        $date['tday'] = $day;
        $date['today'] = $today->toDateString();
        $date['tmonths'] = $months;
        $date['tdata'] =$tdata;

        $date['years'] = $years;

        $date['year'] = $ryear;
        $date['month'] = $rmonth;
        $date['day'] = $rday;
        $date['monthF'] = $this->monthFormat($rmonth);
        $date['date'] = $rdate->toDateString();
        $date['months'] = $rmonths;

        return response()->json(['data' => $data, 'date' => $date]);
    }

    public function monthFormat($month)
    {
        switch ($month) {
            case 1:
                $value = 'Jan.';
                break;
            case 2:
                $value = 'Feb.';
                break;
            case 3:
                $value = 'Mar.';
                break;
            case 4:
                $value = 'Apr.';
                break;
            case 5:
                $value = 'May.';
                break;
            case 6:
                $value = 'June.';
                break;
            case 7:
                $value = 'July.';
                break;
            case 8:
                $value = 'Aug.';
                break;
            case 9:
                $value = 'Sept.';
                break;
            case 10:
                $value = 'Oct.';
                break;
            case 11:
                $value = 'Nov.';
                break;
            default:
                $value = 'Dec';

        }
        return $value;
    }

}
