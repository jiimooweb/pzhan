<?php

namespace App\Http\Controllers\Api\Todays;

use App\Models\Today;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TodayController extends Controller
{

    public function index()
    {
        $data = Today::with('picture')->withCount('todayLikes')->orderBy('today_likes_count', 'DESC')->paginate(20);

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
//         $today = Carbon::today();
        $today = Carbon::parse('2019-02-02');
        $year = $today->year;
        $month = $today->month;
        $day = $today->day;
        $monthf = $this->monthFormat($month);
        $years = [];
        $months = [];
//        $fanID = Token::getUid();
        $fanID = 2;
        if ($year > 2018) {
            for ($i = $year; $i >= 2018; $i--) {
                array_push($years, $i);
            }
            for ($i = $month; $i > 0; $i--) {
                array_push($months, $i);
            }

        } else {
            array_push($years, 2018);
            for ($i = $month; $i > 9; $i--) {
                array_push($months, $i);
            }
        }

        $data = Today::where('date', $today)->withCount('todayLikes')
            ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                $query->where('fan_ID', $fanID);
            }])->with('picture')->get();
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
        $today = Carbon::parse('2019-02-02');
        $fanID = 2;
        if ($caDate->month == $today->month) {
            $data = Today::whereBetween('date', [$firstDay, $today])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])->with('picture')->orderBy('date', 'desc')->get();
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
                }])->with('picture')->orderBy('date', 'desc')->get();
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
        $today = Carbon::parse('2019-02-02');
        $string = $list['year'] . '-' . $list['month'] . '-01';
        $firstDay = Carbon::parse($string);
        $lastDay = Carbon::parse($string)->lastOfMonth();
        $fanID = 2;

        if ($today->month == $list['month'] && $today->year == $list['year']) {
            $data = Today::whereBetween('date', [$firstDay, $today])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])->with('picture')->orderBy('date', 'desc')->get();
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
                }])->with('picture')->orderBy('date', 'desc')->get();
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
        $today = Carbon::parse('2019-02-02');
        $month = $today->month;
        $months = [];
//        $fanID = Token::getUid();
        $fanID = 2;
        if ($reYear > 2018) {
            for ($i = $month; $i > 0; $i--) {
                array_push($months, $i);
            }
            $date['day'] = $today->day;
            $date['month'] = $month;
            $date['months'] = $months;
            $date['monthF'] = $this->monthFormat($month);

            $firstDay = Carbon::parse('2019-02-02')->firstOfMonth();
            $data = Today::whereBetween('date', [$firstDay, $today])
                ->withCount('todayLikes')
                ->withCount(['todayLikes as isLike' => function ($query) use ($fanID) {
                    $query->where('fan_ID', $fanID);
                }])->with('picture')->orderBy('date', 'desc')->get();
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
            for ($i = 12; $i > 9; $i--) {
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
                }])->with('picture')->orderBy('date', 'desc')->get();
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
