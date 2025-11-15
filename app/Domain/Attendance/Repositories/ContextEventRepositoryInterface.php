<?php

namespace App\Domain\Attendance\Repositories;

use Carbon\CarbonInterface;

interface ContextEventRepositoryInterface
{
    /**
     * Devuelve true si el empleado tiene algún evento
     * de contexto (nota, justificación, permiso, comisión,
     * teletrabajo, etc.) que afecte la asistencia en la fecha dada.
     */
    public function hasEventForDate(int $employeeId, CarbonInterface $date): bool;

    /**
     * Retorna todos los eventos de contexto para esa fecha.
     *
     * Cada evento puede incluir:
     * - type: 'note', 'commission', 'telework', etc.
     * - description
     * - source (manual, sistema viejo, suan, etc.)
     */
    public function getEventsForDate(int $employeeId, CarbonInterface $date): array;
}
