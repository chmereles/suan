<?php

namespace App\Domain\Attendance\Models;

use App\Domain\Attendance\Enums\DailyStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SuanDailySummary
 *
 * Representa el resultado final consolidado de un día (para liquidación)
 *
 * @property int $id
 * @property int $labor_link_id
 * @property \Carbon\Carbon $date
 * @property string $status
 * @property int $worked_minutes
 * @property bool $justified
 * @property string|null $notes
 */
class SuanDailySummary extends Model
{
    protected $table = 'suan_daily_summary';

    protected $fillable = [
        'labor_link_id',
        'date',
        'status',
        'worked_minutes',
        'justified',
        'notes',
    ];

    protected $casts = [
        'status' => DailyStatus::class,
        'date' => 'date',
        'worked_minutes' => 'integer',
        'justified' => 'boolean',
    ];

    /** Relaciones */
    public function laborLink()
    {
        return $this->belongsTo(SuanLaborLink::class, 'labor_link_id', 'id');
    }

    /** Scopes */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
