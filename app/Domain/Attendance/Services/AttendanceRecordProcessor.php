<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\DTO\ProcessedRecordDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceRecordProcessor
{
    /**
     * Procesa los logs crudos de un empleado en una fecha,
     * y devuelve registros normalizados para guardarlos en
     * suan_attendance_records.
     */
    public function processEmployeeLogs(
        Collection $logs,
        int $employeeId,
        string $date
    ): array {
        if ($logs->isEmpty()) {
            return [];
        }

        // Ordenar por timestamp
        $logs = $logs->sortBy('recorded_at')->values();

        $results = [];

        foreach ($logs as $log) {

            $recordedAt = Carbon::parse($log->recorded_at);

            $rawPayload = $log->raw_payload;

            if (is_string($rawPayload)) {
                $rawPayload = json_decode($rawPayload, true) ?? [];
            }

            $results[] = new ProcessedRecordDTO(
                employeeId: $employeeId,
                date: $date,
                type: $this->inferTimeSegment($recordedAt),   // mañana / tarde
                recordedAt: $recordedAt->toDateTimeString(),
                attendanceLogId: $log->id ?? null,
                rawId: $log->raw_id ?? null,
                rawPayload: $rawPayload,
                metadata: [
                    'raw_id' => $log->raw_id ?? null,
                    'device' => $log->device_serial ?? null,
                    'record_type' => $log->record_type ?? null,
                    'raw_payload' => $log->raw_payload ?? null,
                ],
            );
        }

        return $results;
    }

    /**
     * Clasifica una marca según hora (mañana/tarde).
     */
    private function inferTimeSegment(Carbon $ts): string
    {
        return $ts->hour < 12
            ? 'morning'
            : 'afternoon';
    }
}
