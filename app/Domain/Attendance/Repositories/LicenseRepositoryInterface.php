<?php

namespace App\Domain\Attendance\Repositories;

use Carbon\CarbonInterface;

interface LicenseRepositoryInterface
{
    /**
     * Devuelve true si el empleado tiene licencia
     * para la fecha indicada.
     */
    public function hasLicenseForDate(int $employeeId, CarbonInterface $date): bool;

    /**
     * Devuelve la lista de licencias activas en ese día.
     *
     * Cada licencia puede incluir:
     * - type (enfermedad, examen, cuidado familiar, etc.)
     * - description
     * - source (sistema viejo, SUAN, manual)
     * - range (fecha desde / hasta)
     */
    public function getLicensesForDate(int $employeeId, CarbonInterface $date): array;
}
