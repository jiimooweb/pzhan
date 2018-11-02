<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Model;

class Social extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('age', function(Builder $builder) {
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

    public function photos()
    {
        return $this->hasMany(Photo::class, 'social_id');
    }

    public function comments()
    {
        return $this->hasMany(SocialComment::class);
    }

    public function likeFans()
    {
        return $this->hasMany(SocialLike::class);
    }

    public function like(int $fan_id) 
    {
        return $this->hasOne(SocialLike::class)->where(['fan_id' => $fan_id, 'social_id' => $this->id ]);
    }

    public function isLike(int $fan_id) 
    {
        return SocialLike::where(['fan_id' => $fan_id, 'social_id' => $this->id ])->exists();
    }


}
