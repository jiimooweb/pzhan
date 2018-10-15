<?php

namespace App\Models;

use App\Models\Model;

class Picture extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'picture_tags', 'picture_id', 'tag_id');
    }

    public function isCollect($fan_id) 
    {
       return CollectPicture::where(['fan_id' => $fan_id, 'picture_id' => $this->id ])->exists();
    }

    public function isLike($fan_id) 
    {
        return LikePicture::where(['fan_id' => $fan_id, 'picture_id' => $this->id ])->exists();
    }

    public function collect($fan_id) 
    {
        return $this->hasOne(CollectPicture::class)->where(['fan_id' => $fan_id, 'picture_id' => $this->id ]);
    }

    public function like($fan_id) 
    {
        return $this->hasOne(LikePicture::class)->where(['fan_id' => $fan_id, 'picture_id' => $this->id ]);
    }

    public function collectFans() 
    {
        return $this->belongsToMany(Fan::class, 'fan_collect_pictures', 'picture_id', 'fan_id');
    }

    public function likeFans() 
    {
        return $this->belongsToMany(Fan::class, 'fan_like_pictures', 'picture_id', 'fan_id');
    }

}
