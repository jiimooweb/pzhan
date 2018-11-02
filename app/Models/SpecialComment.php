<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialComment extends Model
{
    use SoftDeletes;
    protected $guarded=[];
    protected $table = 'special_comments';
    protected $dates = ['delete_at'];

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

    public function blacklists()
    {
        return $this->hasMany(Blacklist::class, 'fan_id', 'fan_id');
    }
}
