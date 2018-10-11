<?php

namespace App\Models;

use App\Models\Model;

class Photo extends Model
{
    protected $table = 'fan_photos';

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function social()
    {
        return $this->belongsTo(Social::class);
    }
}
