<?php

namespace App\Domain\Attendance\Repositories;

use Carbon\CarbonInterface;

interface AttendanceLogRepositoryInterface
{
    /**
     * Devuelve todos los logs crudos de un empleado y una fecha.
     *
     * Cada elemento del array debe tener como mínimo:
     * - id
     * - recorded_at
     * - raw_id
     * - raw_payload
     *
     * @param string|int $deviceUserId
     * @param string     $date Y-m-d
     * @return array
     */
    public function forEmployeeAndDate(string|int $deviceUserId, string $date): array;
}
