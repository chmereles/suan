<?php

namespace App\Domain\Attendance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SuanContextEvent
 *
 * Notas, observaciones, comisiones, justificaciones, etc.,
 * asociadas a un vínculo laboral (labor_link_id).
 *
 * @property int $id
 * @property int $labor_link_id
 * @property \Carbon\Carbon $date
 * @property string $type
 * @property string|null $source
 * @property string|null $description
 * @property array|null $metadata
 * @property int|null $created_by
 */
class SuanContextEvent extends Model
{
    protected $table = 'suan_context_events';

    protected $fillable = [
        'labor_link_id',
        'date',
        'type',
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
    public function laborLink(): BelongsTo
    {
        return $this->belongsTo(SuanLaborLink::class, 'labor_link_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Scopes */

    public function scopeForLaborLinkAndDate($query, int $laborLinkId, string $date)
    {
        return $query->where('labor_link_id', $laborLinkId)
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
