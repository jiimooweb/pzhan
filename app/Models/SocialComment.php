<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialComment extends Model
{

    use SoftDeletes;
    protected $dates = ['delete_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('hidden', function(Builder $builder) {
            $builder->where('hidden', 0);
        });
    }

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
