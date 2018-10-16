<?php

namespace App\Models;

use App\Models\Model;

class Today extends Model
{
    protected $table = 'todays';
    protected $guarded=[];

    public function todayLikes()
    {
        return $this->hasMany(TodayLike::class,'today_id','id');
    }

    public function picture()
    {
        return $this->hasOne(Picture::class,'id','img_id');
    }
}
