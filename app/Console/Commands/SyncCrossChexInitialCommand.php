<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\SyncCrossChexLogsAction;
use App\Domain\Attendance\Services\AttendanceSyncLogger;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncCrossChexInitialCommand extends Command
{
    protected $signature = 'attendance:sync-crosschex-initial 
        {start : Fecha inicio (YYYY-MM-DD)}
        {end   : Fecha fin (YYYY-MM-DD)}';

    protected $description = 'Sincronización inicial masiva de CrossChex (mes a mes).';

    public function handle(SyncCrossChexLogsAction $action): int
    {
        $start = Carbon::parse($this->argument('start'))->startOfMonth();
        $end = Carbon::parse($this->argument('end'))->endOfMonth();

        $this->info("Sincronización inicial desde {$start->toDateString()} a {$end->toDateString()}");

        while ($start->lte($end)) {
            $batchStart = $start->clone()->startOfMonth();
            $batchEnd = $start->clone()->endOfMonth();

            $this->info('Procesando mes: '.$batchStart->format('Y-m'));

            $logger = new AttendanceSyncLogger('crosschex', 'initial');
            $logger->start(null);

            try {
                $inserted = $action->syncRange($batchStart, $batchEnd);
                $logger->success($inserted);

                $this->info("Mes {$batchStart->format('Y-m')} → {$inserted} registros");

            } catch (\Throwable $e) {
                $logger->error($e);
                $this->error("Error en mes {$batchStart->format('Y-m')}: {$e->getMessage()}");
                report($e);
            }

            $start->addMonth();
        }

        $this->info('Sincronización inicial finalizada.');

        return self::SUCCESS;
    }
}
