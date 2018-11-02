<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Special extends Model
{
    protected $table = 'specials';
    protected $guarded=[];

    public function imgs()
    {
        return $this->hasManyThrough(Picture::class,SpecialImg::class,'special_id','id','id','img_id');
    }

    public function cover_img()
    {
        return $this->hasOne(Picture::class,'id','cover');
    }

    public function comments()
    {
        return $this->hasMany(SpecialComment::class,'special_id','id');
    }

}
