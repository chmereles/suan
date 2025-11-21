<?php

namespace App\Console\Commands\Attendance;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\MonthlySummaryRepositoryInterface;

class CloseMonthCommand extends Command
{
    protected $signature = 'suan:close-month 
                            {period? : Periodo en formato YYYY-MM}';

    protected $description = 'Genera el resumen mensual de asistencia para liquidación.';

    public function handle(
        LaborLinkRepositoryInterface $linksRepo,
        DailySummaryRepositoryInterface $dailyRepo,
        MonthlySummaryRepositoryInterface $monthlyRepo
    ) {
        $period = $this->argument('period')
            ?? now()->subMonth()->format('Y-m');

        [$year, $month] = explode('-', $period);

        // rango completo del mes
        $start = Carbon::create($year, $month, 1)->toDateString();
        $end   = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $this->info("Generando resumen mensual para $period ($start → $end)…");

        $links = $linksRepo->allActive();

        foreach ($links as $link) {

            $daily = collect(
                $dailyRepo->getByLaborLinkBetweenDates($link->id, $start, $end)
            );

            $monthlyRepo->storeOrUpdate($link->id, $period, [
                'present_days' => $daily->where('status', 'present')->count(),
                'absent_unjustified_days' => $daily->where('status', 'absent_unjustified')->count(),
                'absent_justified_days' => $daily->where('status', 'absent_justified')->count(),
                'late_minutes' => $daily->sum('late_minutes'),
                'early_leave_minutes' => $daily->sum('early_leave_minutes'),
                'worked_minutes_total' => $daily->sum('worked_minutes'),
                'metadata' => [
                    'record_count' => $daily->count(),
                    'generated_at' => now()->toDateTimeString(),
                ],
            ]);
        }

        $this->info("Resumen mensual generado correctamente.");

        return Command::SUCCESS;
    }
}
