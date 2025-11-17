<?php

namespace App\Domain\Attendance\Models;

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
        'schedule',
    ];

    protected $casts = [
        'active' => 'boolean',
        'schedule' => 'array',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(SuanPerson::class, 'person_id');
    }

    // public static function boot()
    // {
    //     parent::boot();

    //     // Debugging the connection resolver
    //     dd(static::$resolver);
    // }
}