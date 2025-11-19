<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;

class SuanMonthlySummary extends Model
{
    protected $table = 'suan_monthly_summary';

    protected $fillable = [
        'labor_link_id',
        'period',
        'present_days',
        'absent_unjustified_days',
        'absent_justified_days',
        'late_minutes',
        'early_leave_minutes',
        'worked_minutes_total',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function laborLink()
    {
        return $this->belongsTo(SuanLaborLink::class, 'labor_link_id');
    }
}
