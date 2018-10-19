<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use App\Models\Model;

class TodayLike extends Model
{
    //
    protected $table = 'today_likes';
    protected $guarded=[];

    public function fans()
    {
        return $this->hasMany(Fan::class,'fan_id','id')->select('id', 'nickname');;
    }
}
