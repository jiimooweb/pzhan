<?php

namespace App\Models;

use App\Models\Model;

class Picture extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'picture_tags', 'picture_id', 'tag_id');
    }
}
