<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Repositories\AttendanceLogRepositoryInterface;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use App\Domain\Attendance\Repositories\PersonRepositoryInterface;
use App\Domain\Attendance\Services\AttendanceRecordProcessor;

class ProcessAttendanceRecordsAction
{
    public function __construct(
        private PersonRepositoryInterface $personRepo,
        private LaborLinkRepositoryInterface $laborLinkRepo,
        private AttendanceLogRepositoryInterface $logRepo,
        private AttendanceRecordRepositoryInterface $recordRepo,
        private AttendanceRecordProcessor $processor
    ) {}

    public function __invoke(string $deviceUserId, string $date): void
    {
        // 1 — Buscar persona por ID biométrico
        $person = $this->personRepo->findByDeviceUserId($deviceUserId);

        if (! $person) {
            return;
        }

        // 2 — Vinculos laborales activos
        $laborLinks = $this->laborLinkRepo->getActiveByPersonId($person->id);

        if ($laborLinks->isEmpty()) {
            return;
        }

        // 3 — Logs brutos de ese día
        $logs = collect(
            $this->logRepo->getByDeviceUserAndDate($deviceUserId, $date)
        );

        // 4 — Procesar para cada vínculo laboral
        foreach ($laborLinks as $link) {

            // 4.1 — Limpiar los registros procesados previos
            $this->recordRepo->deleteByLaborLinkAndDate($link->id, $date);

            // 4.2 — Interpretar los logs según el horario del vínculo
            $processed = $this->processor->processLaborLinkLogs(
                logs: $logs,
                laborLinkId: $link->id,
                date: $date
            );

            // 4.3 — Almacenar cada registro generado
            foreach ($processed as $dto) {
                $this->recordRepo->store($dto);
            }
        }
    }
}
