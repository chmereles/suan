<?php

namespace App\Domain\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class SuanEmployee
 *
 * Representa a un empleado sincronizado desde Haberes hacia SUAN.
 *
 * @property int $id
 * @property string $legajo
 * @property string|null $cuil
 * @property string $full_name
 * @property string|null $area
 * @property string|null $device_user_id
 * @property bool $active
 * @property \Carbon\Carbon|null $synced_at
 */
class SuanEmployee extends Model
{
    protected $table = 'suan_employees';

    protected $fillable = [
        'legajo',
        'cuil',
        'full_name',
        'area',
        'device_user_id',
        'active',
        'synced_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'synced_at' => 'datetime',
    ];

    /** Relaciones */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(SuanAttendanceRecord::class, 'employee_id');
    }

    public function dailySummaries(): HasMany
    {
        return $this->hasMany(SuanDailySummary::class, 'employee_id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(SuanLicense::class, 'employee_id');
    }

    public function manualNotes(): HasMany
    {
        return $this->hasMany(SuanManualNote::class, 'employee_id');
    }

    public function anomalies(): HasMany
    {
        return $this->hasMany(SuanAnomaly::class, 'employee_id');
    }

    /** Scopes */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByDeviceId($query, string $deviceId)
    {
        return $query->where('device_user_id', $deviceId);
    }

    public function scopeByLegajo($query, string $legajo)
    {
        return $query->where('legajo', $legajo);
    }
}
