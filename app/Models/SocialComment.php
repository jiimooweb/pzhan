<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialComment extends Model
{

    use SoftDeletes;
    protected $dates = ['delete_at'];

    public function getCreatedAtAttribute($date)
    {
        $time = strtotime($date);
        if(time() - $time < 864000) {
            return Carbon::parse($date)->diffForHumans();
        }
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

    public function blacklists()
    {
        return $this->hasMany(Blacklist::class, 'fan_id', 'fan_id');
    }
}
