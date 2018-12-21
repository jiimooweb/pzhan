<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderDate extends Model
{
    //
    protected $table = 'leader_date';
    protected $guarded=[];

    public function leaderboards()
    {
        return $this->hasMany(Leaderboard::class,'date_id','id');
    }
}
