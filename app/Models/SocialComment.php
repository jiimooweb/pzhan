<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Model;

class SocialComment extends Model
{

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }
    
    public function fan()
    {
        return $this->hasOne(Fan::class, 'id', 'fan_id')->select('id', 'nickname', 'avatarUrl');
    }

    public function toFan()
    {
        return $this->hasOne(Fan::class, 'id','to_fan_id')->select('id', 'nickname', 'avatarUrl');
    }

    public function replys()
    {
        return $this->hasMany(SocialComment::class, 'pid', 'id');
    }

}
