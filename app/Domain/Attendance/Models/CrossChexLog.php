<?php

// app/Domain/Attendance/Models/CrossChexLog.php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;

class CrossChexLog extends Model
{
    protected $table = 'crosschex_logs';

    protected $fillable = [
        'uuid',
        'employee_workno',
        'checktype',
        'checktime',
        'device_serial',
        'raw',
    ];

    protected $casts = [
        'checktime' => 'datetime',
        'raw' => 'array',
    ];
}
