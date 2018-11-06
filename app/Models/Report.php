<?php

namespace App\Models;

use App\Models\Model;

class Report extends Model
{
    protected $table = 'reports';

    public function report_fan()
    {
        return $this->hasOne(Fan::class,
            'id', 'reporter_id');
    }

    public function bereport_fan()
    {
        return $this->hasOne(Fan::class,
            'id', 'bereported_id');
    }

    public function cause()
    {
        return $this->hasOne(ReportCause::class,
            'id', 'cause');
    }

    public function comment_s()
    {
            return $this->hasOne(Social::class,
                'id', 'comment')->select(['content']);
    }

    public function comment_sc()
    {
        return $this->hasOne(SocialComment::class,
            'id', 'comment')->select(['content']);
    }

    public function comment_sp()
    {
        return $this->hasOne(SpecialComment::class,
            'id', 'comment')->select(['content']);
    }
}
