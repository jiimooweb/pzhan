<?php

namespace App\Models;

use App\Models\Model;

class Tag extends Model
{
    protected $table = 'tags';

    public function pictures()
    {
        return $this->belongsToMany(Picture::class, 'picture_tags', 'tag_id', 'picture_id');
    }
}
