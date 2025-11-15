<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanDailySummary;

interface DailySummaryRepositoryInterface
{
    /**
     * Obtiene un resumen para empleado + fecha.
     */
    public function findByEmployeeAndDate(int $employeeId, string $date): ?SuanDailySummary;

    /**
     * Crea o actualiza un resumen diario.
     *
     * @param int    $employeeId
     * @param string $date
     * @param array  $data
     */
    public function storeOrUpdate(int $employeeId, string $date, array $data): SuanDailySummary;
}
