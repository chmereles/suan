<?php

namespace App\Domain\Attendance\Repositories;

use Illuminate\Support\Collection;

interface AttendanceLogRepositoryInterface
{
    /**
     * Devuelve todos los logs crudos de CrossChex
     * para un device_user_id en una fecha específica.
     */
    public function getByDeviceUserAndDate(string $deviceUserId, string $date): Collection;

    /**
     * Logs en un rango de fechas.
     */
    public function getRange(string $from, string $to): Collection;
}
