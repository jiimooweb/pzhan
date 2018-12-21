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

    public function child()
    {
        return $this->hasMany(Leaderboard::class,'sid','img_id');
    }

    public function allChildrens(){
        return $this->child()->with('allChildrens');
    }

}
