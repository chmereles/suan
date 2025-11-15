<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SuanAttendanceRecord
 *
 * Registro ya procesado de asistencia (entrada/salida emparejada)
 *
 * @property int $id
 * @property int $employee_id
 * @property \Carbon\Carbon $date
 * @property \Carbon\Carbon|null $check_in
 * @property \Carbon\Carbon|null $check_out
 * @property int $worked_minutes
 * @property int $late_minutes
 * @property int $early_leave_minutes
 * @property string $source
 * @property array|null $metadata
 */
class SuanAttendanceRecord extends Model
{
    protected $table = 'suan_attendance_records';

    protected $fillable = [
        'employee_id',
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
        'date'                 => 'date',
        'check_in'             => 'datetime',
        'check_out'            => 'datetime',
        'worked_minutes'       => 'integer',
        'late_minutes'         => 'integer',
        'early_leave_minutes'  => 'integer',
        'metadata'             => 'array',
    ];

    /** Relaciones */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(SuanEmployee::class, 'employee_id');
    }

    /** Scopes */

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
