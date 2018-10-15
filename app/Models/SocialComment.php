<?php

namespace App\Models;

use App\Models\Model;

class SocialComment extends Model
{
    public function fan()
    {
        return $this->hasOne(Fan::class, 'id', 'fan_id')->select('id', 'nickname');
    }

    public function toFan()
    {
        return $this->hasOne(Fan::class, 'id','to_fan_id')->select('id', 'nickname');
    }

}
