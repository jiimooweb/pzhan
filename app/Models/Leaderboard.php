<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    //
    protected $table = 'leaderboards';
    protected $guarded=[];

    public function picture()
    {
        return $this->hasOne(Picture::class,'id','img_id');
    }
}
