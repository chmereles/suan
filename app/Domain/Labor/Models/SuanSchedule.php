<?php

namespace App\Domain\Labor\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuanSchedule extends Model
{
    use HasFactory;
    
    protected $table = 'suan_schedules';

    protected $fillable = [
        'name',
        'description',
        'type',
        'rotation_length_days',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function workdays(): HasMany
    {
        return $this->hasMany(SuanScheduleWorkday::class, 'schedule_id');
    }
}
