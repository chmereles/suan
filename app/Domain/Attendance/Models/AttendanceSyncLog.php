<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSyncLog extends Model
{
    protected $table = 'attendance_sync_logs';

    protected $fillable = [
        'source',
        'triggered_by',
        'window_minutes',
        'inserted_count',
        'started_at',
        'finished_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
