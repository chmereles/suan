<?php

namespace App\Domain\Labor\Services;

use App\Domain\Labor\Models\SuanSchedule;
use Carbon\Carbon;

class ScheduleResolverService
{
    public function resolveFor(
        SuanSchedule $schedule,
        Carbon $date,
        ?Carbon $rotationStart = null
    ): array {
        $weekday = $date->dayOfWeek; // 0 = Domingo (ajustás si querés)

        if ($schedule->type === 'fixed') {
            return $schedule->workdays
                ->where('weekday', $weekday)
                ->where('is_working_day', true)
                ->sortBy('segment_index')
                ->values()
                ->all();
        }

        // Rotativos → Fase 2
        return [];
    }
}
