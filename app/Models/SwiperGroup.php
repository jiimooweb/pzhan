<?php

namespace App\Models;

use App\Models\Model;

class SwiperGroup extends Model
{
    public $timestamps = false;

    public function swipers() 
    {
        return $this->hasMany(Swiper::class, 'group', 'id');
    }
}
