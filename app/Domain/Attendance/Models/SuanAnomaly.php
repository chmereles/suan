<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SuanAnomaly
 *
 * Evento sospechoso o inusual en la asistencia que requiere revisión humana
 *
 * @property int $id
 * @property int $labor_link_id
 * @property \Carbon\Carbon $date
 * @property string|null $anomaly_type
 * @property string|null $description
 * @property bool $resolved
 * @property int|null $resolved_by
 */
class SuanAnomaly extends Model
{
    protected $table = 'suan_anomalies';

    protected $fillable = [
        'labor_link_id',
        'date',
        'anomaly_type',
        'description',
        'resolved',
        'resolved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'resolved' => 'boolean',
    ];

    /** Relaciones */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(SuanLaborLink::class, 'labor_link_id');
    }

    /** Scopes */
    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }
}
