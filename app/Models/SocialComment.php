<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialComment extends Model
{
    use SoftDeletes;
    protected $dates = ['delete_at'];

    public function fan()
    {
        return $this->hasOne(Fan::class, 'id', 'fan_id')->select('id', 'nickname');
    }

    public function toFan()
    {
        return $this->hasOne(Fan::class, 'id','to_fan_id')->select('id', 'nickname');
    }

}
