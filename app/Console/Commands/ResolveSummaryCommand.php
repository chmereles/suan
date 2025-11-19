<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResolveSummaryCommand extends Command
{
    protected $signature = 'suan:summary:daily {date? : Fecha a procesar (YYYY-MM-DD)}';

    protected $description = 'Genera o recalcula el resumen diario de asistencia (suan_daily_summary) para todos los vínculos laborales activos.';

    public function handle(
        ResolveDailySummaryAction $action,
        LaborLinkRepositoryInterface $laborLinks
    ) {
        $startedAt = microtime(true);

        $dateArg = $this->argument('date');
        $date = $dateArg ? Carbon::parse($dateArg)->toDateString()
                         : Carbon::yesterday()->toDateString();

        $this->info("========== Resolviendo resumen diario para $date ==========");

        $activeLinks = $laborLinks->allActive();
        $count = $activeLinks->count();

        foreach ($activeLinks as $link) {
            $action->execute($link->id, $date);
        }

        $elapsed = round(microtime(true) - $startedAt, 2);

        $this->info("✔ Resumen generado para $count vínculos laborales activos.");
        $this->info("⏱ Tiempo total: {$elapsed}s");

        return self::SUCCESS;
    }
}
