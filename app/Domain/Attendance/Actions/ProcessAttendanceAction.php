<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use App\Domain\Attendance\Repositories\PersonRepositoryInterface;
use App\Domain\Attendance\Services\AttendanceRecordProcessor;
use Illuminate\Support\Collection;

/**
 * Caso de uso: Procesar los logs brutos (por device_user_id)
 * y generar registros interpretados por vínculo laboral.
 */
class ProcessAttendanceAction
{
    public function __construct(
        private PersonRepositoryInterface $personRepo,
        private LaborLinkRepositoryInterface $laborLinks,
        private AttendanceRecordRepositoryInterface $records,
        private AttendanceRecordProcessor $processor
    ) {}

    /**
     * @param  string  $deviceUserId  → ID biométrico
     * @param  string  $date  → YYYY-MM-DD
     * @param  Collection  $logs  → logs crudos filtrados por device_user_id
     */
    public function execute(string $deviceUserId, string $date, Collection $logs): void
    {
        // 1. Encontrar persona por deviceUserId
        $person = $this->personRepo->findByDeviceUserId($deviceUserId);

        if (! $person) {
            // Si no existe la persona todavía, ignorar logs
            return;
        }

        // 2. Obtener vínculos laborales activos de la persona
        $activeLinks = $this->laborLinks->getActiveByPersonId($person->id);

        if ($activeLinks->isEmpty()) {
            return;
        }

        // 3. Por cada vínculo laboral, procesar los logs
        foreach ($activeLinks as $link) {

            // 3.1 Limpia registros anteriores de ese vínculo/día
            $this->records->deleteByLaborLinkAndDate($link->id, $date);

            // 3.2 Interpretar los logs según el vínculo
            $processed = $this->processor->processLaborLinkLogs(
                logs: $logs,
                laborLinkId: $link->id,
                date: $date
            );

            // 3.3 Guardar cada registro interpretado
            foreach ($processed as $dto) {
                $this->records->store($dto);
            }
        }
    }
}
