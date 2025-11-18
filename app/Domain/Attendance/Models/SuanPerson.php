<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuanPerson extends Model
{
    use HasFactory;

    protected $table = 'suan_people';

    protected $fillable = [
        'external_id',
        'document',
        'full_name',
    ];

    public function laborLinks(): HasMany
    {
        return $this->hasMany(SuanLaborLink::class, 'person_id');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(SuanAttendanceLog::class, 'person_id');
    }
}
