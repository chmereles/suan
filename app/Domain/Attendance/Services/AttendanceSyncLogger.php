<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Models\AttendanceSyncLog;
use Throwable;

class AttendanceSyncLogger
{
    protected AttendanceSyncLog $log;

    public function __construct(
        protected string $source = 'crosschex',
        protected string $triggeredBy = 'manual',
    ) {}

    /**
     * Registrar inicio de sincronización
     */
    public function start(?int $windowMinutes = null): AttendanceSyncLog
    {
        $this->log = AttendanceSyncLog::create([
            'source' => $this->source,
            'triggered_by' => $this->triggeredBy,
            'window_minutes' => $windowMinutes,
            'status' => 'running',
            'started_at' => now(),
        ]);

        return $this->log;
    }

    /**
     * Registrar éxito de sincronización
     */
    public function success(int $inserted): void
    {
        $this->log->update([
            'inserted_count' => $inserted,
            'finished_at' => now(),
            'status' => 'success',
        ]);
    }

    /**
     * Registrar error
     */
    public function error(Throwable $e): void
    {
        $this->log->update([
            'finished_at' => now(),
            'status' => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }
}
