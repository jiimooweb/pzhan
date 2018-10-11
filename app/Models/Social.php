<?php

namespace App\Models;

use App\Models\Model;

class Social extends Model
{
    public function photos()
    {
        return $this->hasMany(Photo::class, 'social_id');
    }
}
