<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use Carbon\Carbon;

/**
 * Caso de uso: Registrar una nota externa / evento de contexto
 * que afecte la interpretación de la asistencia.
 */
class RegisterContextEventAction
{
    public function __construct(
        private ContextEventRepositoryInterface $contextEvents
    ) {}

    /**
     * @param  int         $employeeId
     * @param  string      $date        Y-m-d
     * @param  string      $type        commission, rrhh_note, supervisor_note, etc.
     * @param  string|null $description Texto de la nota
     * @param  array       $metadata    Info adicional opcional
     * @param  int|null    $createdBy   ID usuario SUAN
     */
    public function __invoke(
        int $employeeId,
        string $date,
        string $type,
        ?string $description = null,
        array $metadata = [],
        ?int $createdBy = null
    ) {
        return $this->contextEvents->store([
            'employee_id' => $employeeId,
            'date'        => Carbon::parse($date)->toDateString(),
            'type'        => $type,
            'source'      => $metadata['source'] ?? 'manual',
            'description' => $description,
            'metadata'    => $metadata,
            'created_by'  => $createdBy,
        ]);
    }
}
