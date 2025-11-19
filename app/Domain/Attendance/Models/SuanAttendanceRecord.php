<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $labor_link_id
 * @property string $date
 * @property string $recorded_at
 * @property string|null int $type
 * @property int|null $attendance_log_id
 * @property string|null $metadata
 * @property string|null $source
 */
class SuanAttendanceRecord extends Model
{
    protected $table = 'suan_attendance_records';

    protected $fillable = [
        'labor_link_id',
        'date',
        'recorded_at',
        'type',
        'attendance_log_id',
        'metadata',
        'source',
    ];

    protected $casts = [
        'metadata' => 'array',
        'recorded_at' => 'datetime',
        'date' => 'date',
    ];
}
