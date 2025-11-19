<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\DTO\ProcessedRecordDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceRecordProcessor
{
    /**
     * Procesa los logs crudos de una persona para un vínculo laboral
     * en una fecha determinada, y devuelve registros normalizados
     * para guardarlos en suan_attendance_records.
     *
     * NOTA: por ahora solo clasifica en "morning" / "afternoon".
     * Más adelante acá se incorporará la lógica de horarios del labor link.
     */
    public function processLaborLinkLogs(
        Collection $logs,
        int $laborLinkId,
        string $date
    ): array {
        if ($logs->isEmpty()) {
            return [];
        }

        // Ordenar por timestamp
        $logs = $logs->sortBy('recorded_at')->values();

        $results = [];

        foreach ($logs as $log) {
            // 1. Parsear timestamp crudo (UTC)
            $recordedAt = Carbon::parse($log->recorded_at)
                ->setTimezone(config('app.timezone')); // <-- CONVERSIÓN AQUÍ

            $results[] = new ProcessedRecordDTO(
                laborLinkId: $laborLinkId,
                date: $date,
                recordedAt: $recordedAt->toDateTimeString(),
                type: $this->inferTimeSegment($recordedAt),   // por ahora: mañana / tarde
                attendanceLogId: $log->id ?? null,
                metadata: [
                    'raw_id' => $log->raw_id ?? null,
                    'device' => $log->device_serial ?? null,
                    'record_type' => $log->record_type ?? null,
                    // 'raw_payload' => $log->raw_payload ?? null,
                ],
            );
        }

        return $results;
    }

    /**
     * Clasifica una marca según hora (mañana/tarde).
     * (esto después lo podés reemplazar por lógica basada en horarios del labor link)
     */
    private function inferTimeSegment(Carbon $ts): string
    {
        return $ts->hour < 12
            ? 'morning'
            : 'afternoon';
    }
}
