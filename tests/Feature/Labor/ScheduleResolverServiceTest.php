<?php

use App\Domain\Labor\Models\SuanSchedule;
use App\Domain\Labor\Models\SuanScheduleWorkday;
use App\Domain\Labor\Services\ScheduleResolverService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('resolves windows for a fixed schedule', function () {
    $schedule = SuanSchedule::factory()->create([
        'type' => 'fixed',
        'active' => true,
    ]);

    // Lunes (1)
    SuanScheduleWorkday::factory()->create([
        'schedule_id' => $schedule->id,
        'weekday' => 1,
        'segment_index' => 1,
        'start_time' => '07:00:00',
        'end_time' => '13:00:00',
        'tolerance_in_minutes' => 10,
        'tolerance_out_minutes' => 10,
        'is_working_day' => true,
    ]);

    $resolver = new ScheduleResolverService;

    $date = Carbon::parse('2025-01-06'); // lunes
    $windows = $resolver->resolveFor($schedule, $date);

    expect($windows)->toHaveCount(1);

    $win = $windows[0];

    expect($win['start']->format('H:i'))->toBe('07:00');
    expect($win['end']->format('H:i'))->toBe('13:00');
    expect($win['tolerance_in'])->toBe(10);
    expect($win['tolerance_out'])->toBe(10);
});
