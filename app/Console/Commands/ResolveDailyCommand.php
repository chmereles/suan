<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResolveDailyCommand extends Command
{
    protected $signature = 'suan:resolve-day {date?}';

    protected $description = 'Recalcula únicamente el resumen diario de asistencia.';

    public function handle(
        ResolveDailySummaryAction $summaryAction,
        LaborLinkRepositoryInterface $laborLinks
    ) {
        $date = $this->argument('date') ?? Carbon::yesterday()->toDateString();

        $this->info("========== Recalculando Resumen Diario: $date ==========");

        foreach ($laborLinks->allActive() as $link) {
            $summaryAction->execute($link->id, $date);
        }

        $this->info("Resumen diario recalculado para $date.");

        return self::SUCCESS;
    }
}
