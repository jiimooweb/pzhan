<?php

namespace App\Models;

use App\Models\Model;

class SocialComment extends Model
{
    public function fan()
    {
        $this->hasOne(Fan::class, 'fan_id');
    }

    public function toFan()
    {
        $this->hasOne(Fan::class, 'to_fan_id');
    }

}
