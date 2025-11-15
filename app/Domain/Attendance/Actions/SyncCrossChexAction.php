<?php

namespace App\Domain\Attendance\Actions;

use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use App\Domain\Attendance\Services\AttendanceProcessorService;
use App\Infrastructure\Attendance\CrossChex\CrossChexClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Caso de uso: Sincronizar CrossChex → Attendance Logs → Attendance Records
 *
 * Este Action es el "pipeline" completo de sincronización de SUAN.
 */
class SyncCrossChexAction
{
    public function __construct(
        private CrossChexClient $client,
        private AttendanceProcessorService $processor,
        private EmployeeRepositoryInterface $employeeRepo,
        private AttendanceRecordRepositoryInterface $attendanceRepo,
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
            $logs = collect($this->client->getAllRecords($start, $end));

            $grouped = $logs->groupBy(function ($log) {
                return $log['device_user_id'].'-'.substr($log['recorded_at'], 0, 10);
            });

            $processed = 0;

            foreach ($grouped as $key => $items) {
                [$deviceUserId, $date] = explode('-', $key);

                $this->processor->processEmployeeLogs(
                    logs: $items,
                    employeeId: optional($this->employeeRepo->findByDeviceUserId($deviceUserId))->id,
                    date: $date
                );

                $processed++;
            }

            DB::table('attendance_sync_logs')
                ->where('id', $syncId)
                ->update([
                    'finished_at' => now(),
                    'inserted_count' => $processed,
                    'status' => 'ok',
                ]);

            return [
                'status' => 'ok',
                'processed' => $processed,
            ];

        } catch (\Throwable $e) {

            DB::table('attendance_sync_logs')
                ->where('id', $syncId)
                ->update([
                    'finished_at' => now(),
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);

            throw new RuntimeException(
                'Error en SyncCrossChexAction: '.$e->getMessage(),
                previous: $e
            );
        }
    }
}
