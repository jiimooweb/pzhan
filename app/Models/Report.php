<?php

namespace App\Models;

use App\Models\Model;

class Report extends Model
{
    protected $table = 'reports';

    public function report_fan()
    {
        $this->hasOne(Fan::class,
            'reporter_id', 'id');
    }

    public function bereport_fan()
    {
        $this->hasOne(Fan::class,
            'bereported_id', 'id');
    }

    public function cause()
    {
        $this->hasOne(ReportCause::class,
            'cause', 'id');
    }
}
