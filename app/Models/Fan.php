<?php

namespace App\Models;

use App\Models\Model;

class Fan extends Model
{
    public function collcetPictures()
    {
        return $this->belongsToMany(Picture::class, 'fan_collect_pictures', 'fan_id', 'picture_id')->distinct()->orderBy('fan_collect_pictures.created_at', 'desc');
    }

    public function likePictures()
    {
        return $this->belongsToMany(Picture::class, 'fan_like_pictures', 'fan_id', 'picture_id')->distinct()->orderBy('fan_like_pictures.created_at', 'desc');
    }

    public function downloadPictures()
    {
        return $this->belongsToMany(Picture::class, 'fan_download_pictures', 'fan_id', 'picture_id')->distinct()->orderBy('fan_download_pictures.created_at', 'desc');
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
