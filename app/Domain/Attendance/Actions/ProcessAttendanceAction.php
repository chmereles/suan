<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use App\Domain\Attendance\Services\AttendanceProcessorService;
use Illuminate\Support\Collection;

/**
 * Caso de uso: Procesar los logs de asistencia para un empleado en una fecha.
 */
class ProcessAttendanceAction
{
    public function __construct(
        private AttendanceProcessorService $processor,
        private EmployeeRepositoryInterface $employeeRepo
    ) {}

    /**
     * @param  string  $deviceUserId  → ID del reloj biométrico
     * @param  string  $date  → Fecha en formato YYYY-MM-DD
     * @param  Collection  $logs  → logs crudos ya filtrados
     */
    public function execute(string $deviceUserId, string $date, Collection $logs)
    {
        $employee = $this->employeeRepo->findByDeviceUserId($deviceUserId);

        if (! $employee) {
            // Se ignoran los logs de empleados no mapeados aún
            return null;
        }

        return $this->processor->processEmployeeLogs(
            logs: $logs,
            employeeId: $employee->id,
            date: $date
        );
    }
}
