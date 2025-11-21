<?php

namespace App\Domain\Labor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuanScheduleWorkday extends Model
{
    protected $table = 'suan_schedule_workdays';

    protected $fillable = [
        'schedule_id',
        'weekday',
        'pattern_index',
        'segment_index',
        'start_time',
        'end_time',
        'tolerance_in_minutes',
        'tolerance_out_minutes',
        'is_working_day',
    ];

    protected $casts = [
        'is_working_day' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SuanSchedule::class);
    }
}
