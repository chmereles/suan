<?php

namespace App\Domain\Attendance\Actions;

use App\Infrastructure\Attendance\CrossChex\CrossChexClient;
use App\Domain\Attendance\Services\CrossChexMapper;
use App\Domain\Attendance\Repositories\AttendanceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SyncCrossChexAction
{
    public function __construct(
        private readonly CrossChexClient $client,
        private readonly CrossChexMapper $mapper,
        private readonly AttendanceRepository $repo,
    ) {}

    public function execute(Carbon $start, Carbon $end): array
    {
        $syncId = DB::table('attendance_sync_logs')->insertGetId([
            'source'        => 'crosschex',
            'triggered_by'  => 'cron',
            'started_at'    => now(),
            'created_at'    => now(),
            'status'        => 'running',
        ]);

        try {

            // 1) Obtener desde CrossChex
            $rawRecords = $this->client->getAllRecords($start, $end);

            // 2) Mapear → normalizar → asegurar claves
            $mapped = $this->mapper->mapRecords($rawRecords);

            // 3) almacenar usando el repositorio estandarizado
            $insertedCount = $this->repo->storeMany($mapped);

            // 4) actualizar estado
            DB::table('attendance_sync_logs')
                ->where('id', $syncId)
                ->update([
                    'finished_at'    => now(),
                    'inserted_count' => $insertedCount,
                    'status'         => 'ok',
                ]);

            return [
                'status'   => 'ok',
                'inserted' => $insertedCount,
            ];

        } catch (\Throwable $e) {

            DB::table('attendance_sync_logs')
                ->where('id', $syncId)
                ->update([
                    'finished_at'   => now(),
                    'status'        => 'error',
                    'error_message' => $e->getMessage(),
                ]);

            throw new RuntimeException(
                'Error en SyncCrossChexAction: '.$e->getMessage(),
                previous: $e
            );
        }
    }
}
