<?php

namespace App\Models;

use App\Models\Model;

class Fan extends Model
{
    public function collcetPictures()
    {
        return $this->belongsToMany(Picture::class, 'fan_collect_pictures', 'fan_id', 'picture_id');
    }

    public function likePictures()
    {
        return $this->belongsToMany(Picture::class, 'fan_like_pictures', 'fan_id', 'picture_id');
    }

    public function album()
    {
        return $this->hasMany(Album::class);
    }

    public static function getByOpenID($openid) 
    {
        return self::where('openid', $openid)->first();
    }
}
