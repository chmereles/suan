<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuanAttendanceLog extends Model
{
    protected $table = 'suan_attendance_logs';

    protected $fillable = [
        'person_id',
        'device_user_id',
        'recorded_at',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'recorded_at' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(SuanPerson::class, 'person_id');
    }
}