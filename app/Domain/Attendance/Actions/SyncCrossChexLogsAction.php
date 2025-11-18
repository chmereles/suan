<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Repositories\AttendanceRepository;
use App\Domain\Attendance\Services\CrossChexMapper;
use App\Infrastructure\Attendance\CrossChex\CrossChexClient;
use Carbon\Carbon;

class SyncCrossChexLogsAction
{
    public function __construct(
        private readonly CrossChexClient $client,
        private readonly CrossChexMapper $mapper,
        private readonly AttendanceRepository $repo,
    ) {}

    /**
     * Ejecutar sync sobre una ventana de minutos
     */
    public function __invoke(?int $windowMinutes = null): int
    {
        [$start, $end] = $this->resolveWindow($windowMinutes);

        return $this->syncRange($start, $end);
    }

    /**
     * Sincronizar entre rango arbitrario (ideal para inicial)
     */
    public function syncRange(Carbon $start, Carbon $end): int
    {
        $records = $clientRecords = $this->client->getAllRecords($start, $end);

        $mapped = $this->mapper->mapRecords($records);

        return $this->repo->storeMany($mapped);
    }

    /**
     * Determinar ventana por minutos
     */
    private function resolveWindow(?int $windowMinutes): array
    {
        $end = now();
        $start = $windowMinutes
            ? now()->clone()->subMinutes($windowMinutes)
            : now()->clone()->subMinutes(15);

        return [$start, $end];
    }
}
