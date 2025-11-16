<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Repositories\AttendanceLogRepositoryInterface;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use App\Domain\Attendance\Services\AttendanceRecordProcessor;

class ProcessAttendanceRecordsAction
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepo,
        private AttendanceLogRepositoryInterface $logRepo,
        private AttendanceRecordRepositoryInterface $recordRepo,
        private AttendanceRecordProcessor $processor
    ) {}

    public function __invoke(string $deviceUserId, string $date): void
    {
        // 1 — Buscar empleado
        $employee = $this->employeeRepo->findByDeviceUserId($deviceUserId);

        if (! $employee) {
            return;
        }

        // 2 — Tomar logs crudos de ese día
        $logs = collect(
            $this->logRepo->getByDeviceUserAndDate($deviceUserId, $date)
        );

        // 3 — Eliminar registros procesados previos
        $this->recordRepo->deleteByEmployeeAndDate($employee->id, $date);

        // 4 — Procesar logs
        $processed = $this->processor->processEmployeeLogs(
            logs: $logs,
            employeeId: $employee->id,
            date: $date
        );

        // 5 — Guardar cada registro procesado
        foreach ($processed as $dto) {
            $this->recordRepo->store($dto);
        }
    }
}
