<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResolveSummaryCommand extends Command
{
    protected $signature = 'suan:resolve-summary 
                            {date? : Fecha a consolidar (YYYY-MM-DD)}';

    protected $description = 'Genera suan_daily_summary para cada vínculo laboral activo.';

    public function handle(
        ResolveDailySummaryAction $action,
        LaborLinkRepositoryInterface $laborLinks
    ) {
        $date = $this->argument('date')
            ?? Carbon::yesterday()->toDateString();

        $this->info("Resolviendo resumen diario para: $date");

        // Recuperar todos los vínculos laborales activos
        $activeLinks = $laborLinks->allActive(); // Collection<SuanLaborLink>

        foreach ($activeLinks as $link) {
            $action->execute($link->id, $date);
        }

        $this->info('Resumen diario resuelto para ' . count($activeLinks) . ' vínculos laborales activos.');

        return self::SUCCESS;
    }
}
