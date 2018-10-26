<?php

namespace App\Models;

use App\Models\Model;

class Notice extends Model
{
    public function fan()
    {
        return $this->hasOne(Fan::class, 'id', 'fan_id')->select('id', 'nickname', 'avatarUrl');
    }

    public function fromFan()
    {
        return $this->hasOne(Fan::class, 'id','from_fan_id')->select('id', 'nickname', 'avatarUrl');
    }
}
