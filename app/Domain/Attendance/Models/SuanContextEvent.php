<?php

namespace App\Domain\Attendance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SuanContextEvent
 *
 * Notas y justificativos cargados por jefes
 *
 * @property int $id
 * @property int $employee_id
 * @property \Carbon\Carbon $date
 * @property string $note_type
 * @property string $content
 * @property int|null $created_by
 */
class SuanContextEvent extends Model
{
    protected $table = 'suan_context_events';

    protected $fillable = [
        'employee_id',
        'date',
        'type_id',
        'source',
        'description',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'metadata' => 'array',
    ];

    /** Relaciones */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(SuanEmployee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForEmployeeAndDate($query, int $employeeId, string $date)
    {
        return $query->where('employee_id', $employeeId)
            ->whereDate('date', $date);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForRange($query, string $from, string $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }
}
