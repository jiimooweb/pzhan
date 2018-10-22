<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table = 'blacklists';
    protected $guarded=[];

    public function fan()
    {
        return $this->hasOne(Fan::class,'id','fan_id')->select('id', 'nickname');
    }
}
