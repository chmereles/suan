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
     */
    public function storeOrUpdate(int $employeeId, string $date, array $data): SuanDailySummary;

    /**
     * Devuelve todos los resúmenes diarios de una fecha,
     * generalmente para mostrarlos en el dashboard.
     */
    public function getByDate(string $date): array;
}
