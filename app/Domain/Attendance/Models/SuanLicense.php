<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SuanLicense
 *
 * Representa una licencia (del sistema de Haberes o cargada manualmente)
 *
 * @property int $id
 * @property int $employee_id
 * @property \Carbon\Carbon $date
 * @property string $type
 * @property string|null $description
 */
class SuanLicense extends Model
{
    protected $table = 'suan_licenses';

    protected $fillable = [
        'employee_id',
        'date',
        'type',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /** Relaciones */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(SuanLaborLink::class, 'labor_link_id');
    }

    /** Scopes */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }
}
