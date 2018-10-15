<?php

namespace App\Models;

use App\Models\Model;

class Fan extends Model
{
    public function collcetPictures()
    {
        return $this->hasMany(Picture::class, 'fan_collect_pictures', 'fan_id', 'picture_id');
    }

    public function likePictures()
    {
        return $this->hasMany(Picture::class, 'fan_like_pictures', 'fan_id', 'picture_id');
    }
}
