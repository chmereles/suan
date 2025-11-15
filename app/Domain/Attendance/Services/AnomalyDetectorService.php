<?php

namespace App\Domain\Attendance\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnomalyDetectorService
{
    /**
     * Detecta anomalías en las marcas procesadas de un día.
     *
     * @param  array|Collection  $records   Registros procesados (suan_attendance_records)
     * @param  int               $employeeId
     * @param  string            $date      YYYY-MM-DD
     * @return array                          Lista de anomalías detectadas
     */
    public function detect(array|Collection $records, int $employeeId, string $date): array
    {
        $records = collect($records);

        $anomalies = [];

        // ------------------------------------------------------------------
        // Caso 1: Día sin marcas (ya resuelto en el resolver, pero lo dejamos)
        // ------------------------------------------------------------------
        if ($records->isEmpty()) {
            $anomalies[] = ['type' => 'no_marks', 'message' => 'No se registraron fichadas.'];
            return $anomalies;
        }

        // Ordenar por seguridad
        $records = $records->sortBy('recorded_at')->values();

        // ------------------------------------------------------------------
        // Caso 2: Marca única (solo entrada o solo salida)
        // ------------------------------------------------------------------
        if ($records->count() === 1) {
            $anomalies[] = [
                'type' => 'single_mark',
                'message' => 'Solo se registró una marca (entrada o salida).'
            ];
        }

        // ------------------------------------------------------------------
        // Caso 3: Marcaciones duplicadas (mismo timestamp)
        // ------------------------------------------------------------------
        $duplicates = $records
            ->groupBy('recorded_at')
            ->filter(fn ($g) => $g->count() > 1);

        foreach ($duplicates as $ts => $group) {
            $anomalies[] = [
                'type' => 'duplicate_marks',
                'timestamp' => $ts,
                'count' => $group->count(),
                'message' => "Se detectaron {$group->count()} marcas duplicadas en $ts."
            ];
        }

        // ------------------------------------------------------------------
        // Caso 4: Orden incorrecto (marcas desordenadas por hora)
        // ------------------------------------------------------------------
        foreach ($records as $idx => $record) {
            if ($idx === 0) {
                continue;
            }

            $prev = Carbon::parse($records[$idx - 1]->recorded_at);
            $curr = Carbon::parse($record->recorded_at);

            if ($curr->lessThan($prev)) {
                $anomalies[] = [
                    'type' => 'out_of_order',
                    'timestamp' => $curr->toDateTimeString(),
                    'message' => 'Las fichadas no están en orden cronológico.'
                ];
            }
        }

        // ------------------------------------------------------------------
        // Caso 5: Gap demasiado grande entre marcas
        // Ej: entrada 08:00, siguiente marca 14:00 → improbable
        // ------------------------------------------------------------------
        for ($i = 1; $i < $records->count(); $i++) {
            $prev = Carbon::parse($records[$i - 1]->recorded_at);
            $curr = Carbon::parse($records[$i]->recorded_at);

            if ($prev->diffInHours($curr) >= 6) {
                $anomalies[] = [
                    'type' => 'large_gap',
                    'from' => $prev->toDateTimeString(),
                    'to' => $curr->toDateTimeString(),
                    'message' => 'Existe un hueco improbable entre dos marcas.',
                ];
            }
        }

        // ------------------------------------------------------------------
        // Caso 6: Falta de salida (última marca muy temprano)
        // ------------------------------------------------------------------
        $first = Carbon::parse($records->first()->recorded_at);
        $last = Carbon::parse($records->last()->recorded_at);

        // Ejemplo: si la jornada termina a las 13:00
        $expectedExit = Carbon::parse($date . ' 13:00:00');

        if ($last->lessThan($expectedExit) && $records->count() >= 1) {
            $anomalies[] = [
                'type' => 'missing_checkout',
                'last_record' => $last->toDateTimeString(),
                'message' => 'No se registró salida completa del turno.',
            ];
        }

        return $anomalies;
    }
}
