<?php

namespace App\Models;

use App\Models\Model;

class ShareHistory extends Model
{
    protected $table = 'share_histories';

    public function getCreatedAtAttribute($date)
    {
        return date('Y-m-d H:i',strtotime($date));
    }

    public function share_fan()
    {
        return $this->hasOne(Fan::class,
            'id', 'share_id');
    }

    public function beshare_fan()
    {
        return $this->hasOne(Fan::class,
            'id', 'beshare_id');
    }

}
