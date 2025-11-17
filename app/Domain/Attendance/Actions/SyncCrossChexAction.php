<?php

namespace App\Domain\Attendance\Actions;

use App\Infrastructure\Attendance\CrossChex\CrossChexClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Caso de uso: Sincronizar CrossChex → attendance_logs
 *
 * Este action SOLO inserta logs crudos.
 * El procesamiento posterior lo realiza ProcessAttendanceAction.
 */
class SyncCrossChexAction
{
    public function __construct(
        private CrossChexClient $client,
    ) {}

    /**
     * Ejecuta la sincronización completa.
     *
     * @return array resumen de sincronización
     */
    public function execute(Carbon $start, Carbon $end): array
    {
        $syncId = DB::table('attendance_sync_logs')->insertGetId([
            'source' => 'crosschex',
            'triggered_by' => 'cron',
            'started_at' => now(),
            'created_at' => now(),
            'status' => 'running',
        ]);

        try {
            // 1 — Descargar todos los registros del período
            $records = collect($this->client->getAllRecords($start, $end));

            // 2 — Insertar logs crudos
            $inserted = 0;

            foreach ($records as $log) {
                DB::table('attendance_logs')->insert([
                    'device_serial'    => $log['device_serial'] ?? null,
                    'device_user_id'   => $log['device_user_id'],
                    'raw_id'           => $log['raw_id'],
                    'raw_payload'      => json_encode($log['raw_payload']),
                    'record_type'      => $log['record_type'] ?? null,
                    'recorded_at'      => $log['recorded_at'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                $inserted++;
            }

            // 3 — Guardar estado OK de la sincronización
            DB::table('attendance_sync_logs')
                ->where('id', $syncId)
                ->update([
                    'finished_at'    => now(),
                    'inserted_count' => $inserted,
                    'status'         => 'ok',
                ]);

            return [
                'status'     => 'ok',
                'inserted'   => $inserted,
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
