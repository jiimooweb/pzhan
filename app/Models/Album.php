<?php

namespace App\Models;

use App\Models\Model;

class Album extends Model
{
    public function photos() 
    {
        return $this->hasMany(Photo::class, 'album_id');
    }
}
