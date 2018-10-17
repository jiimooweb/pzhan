<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialComment extends Model
{
    use SoftDeletes;
    protected $table = 'special_comments';
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
