<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $labor_link_id
 * @property string $date
 * @property string|null $check_in
 * @property string|null $check_out
 * @property int $worked_minutes
 * @property int $late_minutes
 * @property int $early_leave_minutes
 */
class SuanAttendanceRecord extends Model
{
    use HasFactory;

    protected $table = 'suan_attendance_records';

    protected $fillable = [
        'labor_link_id',
        'date',
        'check_in',
        'check_out',
        'worked_minutes',
        'late_minutes',
        'early_leave_minutes',
        'source',
        'metadata',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'metadata' => 'array',
    ];
}
