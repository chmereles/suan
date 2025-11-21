<?php

namespace App\Domain\Attendance\Models;

use App\Domain\Labor\Models\SuanSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuanLaborLink extends Model
{
    use HasFactory;

    protected $table = 'suan_labor_links';

    protected $fillable = [
        'person_id',
        'source',
        'external_id',
        'active',
        'area',
        'position',
        'schedule_id'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(SuanPerson::class, 'person_id');
    }

    public function dailySummaries()
    {
        return $this->hasMany(SuanDailySummary::class, 'labor_link_id');
    }

    public function schedule()
    {
        return $this->belongsTo(SuanSchedule::class, 'schedule_id');
    }
}
