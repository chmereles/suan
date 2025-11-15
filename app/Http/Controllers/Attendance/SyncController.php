<?php 

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Actions\SyncCrossChexLogsAction;
use App\Domain\Attendance\Models\AttendanceSyncLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SyncController
{
    public function index(Request $request): Response
    {
        $logsQuery = AttendanceSyncLog::query()
            ->where('source', 'crosschex')
            ->orderByDesc('started_at');

        $logs = $logsQuery->paginate(15)->through(function (AttendanceSyncLog $log) {
            return [
                'id'             => $log->id,
                'source'         => $log->source,
                'triggered_by'   => $log->triggered_by,
                'window_minutes' => $log->window_minutes,
                'inserted_count' => $log->inserted_count,
                'status'         => $log->status,
                'error_message'  => $log->error_message,
                'started_at'     => optional($log->started_at)->toDateTimeString(),
                'finished_at'    => optional($log->finished_at)->toDateTimeString(),
                'duration_sec'   => $log->started_at && $log->finished_at
                    ? $log->finished_at->diffInSeconds($log->started_at)
                    : null,
            ];
        });

        $lastSync = AttendanceSyncLog::where('source', 'crosschex')
            ->orderByDesc('started_at')
            ->first();

        // Info sencilla de cron (podés mejorarla más adelante)
        $cronInfo = [
            'expected_interval_minutes' => 10,
            'last_cron_run_at' => optional(
                AttendanceSyncLog::where('triggered_by', 'cron')
                    ->orderByDesc('started_at')
                    ->first()
            )?->started_at?->toDateTimeString(),
        ];

        // return Inertia::render('Attendance/Sync/Index');
        return Inertia::render('Attendance/Sync/Index', [
            'logs'      => $logs,
            'lastSync'  => $lastSync ? [
                'status'         => $lastSync->status,
                'inserted_count' => $lastSync->inserted_count,
                'started_at'     => optional($lastSync->started_at)->toDateTimeString(),
                'finished_at'    => optional($lastSync->finished_at)->toDateTimeString(),
                'window_minutes' => $lastSync->window_minutes,
            ] : null,
            'defaultWindow' => 15,
            'cronInfo'      => $cronInfo,
            // 'canRunSync'    => $request->user()?->can('attendance.sync.run') ?? false,
            'canRunSync'    => true,
        ]);
    }

    public function run(Request $request, SyncCrossChexLogsAction $action)
    {
        // $this->authorize('attendance.sync.run');

        $window = $request->integer('window') ?: null;

        $log = AttendanceSyncLog::create([
            'source'         => 'crosschex',
            'triggered_by'   => 'manual',
            'window_minutes' => $window,
            'status'         => 'running',
            'started_at'     => now(),
        ]);

        try {
            $inserted = $action($window); // tu Action actual, sin cambios

            $log->update([
                'inserted_count' => $inserted,
                'finished_at'    => now(),
                'status'         => 'success',
            ]);

            return back()->with('success', "Sincronización completada. Registros nuevos: {$inserted}");
        } catch (\Throwable $e) {
            $log->update([
                'finished_at'   => now(),
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            report($e);

            return back()->with('error', 'Error durante la sincronización: ' . $e->getMessage());
        }
    }
}
