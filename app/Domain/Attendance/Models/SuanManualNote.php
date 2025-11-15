<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SuanManualNote
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
class SuanManualNote extends Model
{
    protected $table = 'suan_manual_notes';

    protected $fillable = [
        'employee_id',
        'date',
        'note_type',
        'content',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /** Relaciones */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(SuanEmployee::class, 'employee_id');
    }

    /** Scopes */
    public function scopeType($query, string $type)
    {
        return $query->where('note_type', $type);
    }
}
