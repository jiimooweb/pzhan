<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Model;

class Notice extends Model
{
    public function getCreatedAtAttribute($date)
    {
        $time = strtotime($date);
        if(time() - $time < 86400) {
            return Carbon::parse($date)->diffForHumans();
        }

        return date('m-d H:i',strtotime($date));
    }
    
    public function fan()
    {
        return $this->hasOne(Fan::class, 'id', 'fan_id')->select('id', 'nickname', 'avatarUrl');
    }

    public function fromFan()
    {
        return $this->hasOne(Fan::class, 'id','from_fan_id')->select('id', 'nickname', 'avatarUrl');
    }
}
