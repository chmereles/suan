<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\DTO\ProcessedRecordDTO;

interface AttendanceRecordRepositoryInterface
{
    /**
     * Elimina todos los registros procesados de un vínculo laboral en un día.
     */
    public function deleteByLaborLinkAndDate(int $laborLinkId, string $date): void;

    /**
     * Inserta un registro procesado.
     */
    public function store(ProcessedRecordDTO $dto): void;

    /**
     * Devuelve todos los registros procesados de un vínculo laboral en una fecha.
     */
    public function getByLaborLinkAndDate(int $laborLinkId, string $date): array;
}
