<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\DTO\ProcessedRecordDTO;

interface AttendanceRecordRepositoryInterface
{
    /**
     * Elimina todos los registros procesados de un empleado en un día.
     */
    public function deleteByEmployeeAndDate(int $employeeId, string $date): void;

    /**
     * Inserta un registro procesado.
     */
    public function store(ProcessedRecordDTO $dto): void;

    /**
     * Devuelve todos los registros procesados de un empleado en una fecha.
     */
    public function getByEmployeeAndDate(int $employeeId, string $date): array;
}
