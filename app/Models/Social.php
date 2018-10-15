<?php

namespace App\Models;

use App\Models\Model;

class Social extends Model
{
    public function photos()
    {
        return $this->hasMany(Photo::class, 'social_id');
    }

    public function comments()
    {
        return $this->hasMany(SocialComment::class)->with(['fan','toFan']);
    }

}
