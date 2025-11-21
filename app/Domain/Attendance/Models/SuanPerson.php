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
        'device_user_id'
    ];

    public function laborLinks(): HasMany
    {
        return $this->hasMany(SuanLaborLink::class, 'person_id');
    }
}
