<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

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
    public static function boot() {
        parent::boot();

        static::addGlobalScope('is_up', function(Builder $builder) {
            $builder->where('is_up','!=',0);
        });

    }


}
