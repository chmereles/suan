<?php

namespace App\Domain\Labor\Services;

use App\Domain\Labor\Models\SuanSchedule;
use Carbon\Carbon;

class ScheduleResolverService
{
    /**
     * Devuelve las ventanas de trabajo esperadas para un día.
     *
     * @return array<int, array{
     *   start: \Carbon\Carbon,
     *   end: \Carbon\Carbon,
     *   tolerance_in: int,
     *   tolerance_out: int
     * }>
     */
    public function resolveFor(
        SuanSchedule $schedule,
        Carbon $date,
        ?Carbon $rotationStart = null
    ): array {
        // Asegurarnos de tener workdays cargados
        $schedule->loadMissing('workdays');

        $weekday = $date->dayOfWeek; // 0 = domingo (ajustá si tu DB usa otra convención)

        if ($schedule->type === 'fixed') {
            $workdays = $schedule->workdays
                ->where('weekday', $weekday)
                ->where('is_working_day', true)
                ->sortBy('segment_index')
                ->values();

            if ($workdays->isEmpty()) {
                // Día no laborable según contrato
                return [];
            }

            // Normalizamos a ventanas (sin exponer Eloquent a Attendance)
            return $workdays->map(function ($wd) use ($date) {
                // $wd->start_time y end_time vienen como 'HH:MM:SS' (o HH:MM)
                $start = Carbon::parse($date->toDateString() . ' ' . $wd->start_time);
                $end   = Carbon::parse($date->toDateString() . ' ' . $wd->end_time);

                return [
                    'start'         => $start,
                    'end'           => $end,
                    'tolerance_in'  => (int) $wd->tolerance_in_minutes,
                    'tolerance_out' => (int) $wd->tolerance_out_minutes,
                ];
            })->all();
        }

        // Rotativos → Fase 2
        return [];
    }
}
