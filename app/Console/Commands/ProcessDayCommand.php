<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ProcessAttendanceAction;
use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Actions\SyncCrossChexAction;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessDayCommand extends Command
{
    protected $signature = 'suan:process-day {date?}';
    protected $description = 'Ejecuta sincronización + procesamiento + resumen para un día.';

    public function handle(
        SyncCrossChexAction $sync,
        ProcessAttendanceAction $processAction,
        ResolveDailySummaryAction $summaryAction,
        LaborLinkRepositoryInterface $laborLinks
    ) {
        $date = $this->argument('date')
            ?? Carbon::yesterday()->toDateString();

        $this->info("========== Procesando Día Completo: $date ==========");

        /* ---------------------------------------------------------
         * 1. Sincronizar logs crudos desde CrossChex
         * ---------------------------------------------------------*/
        $this->info('Sincronizando logs del día...');
        $sync->execute(
            Carbon::parse("$date 00:00:00"),
            Carbon::parse("$date 23:59:59")
        );

        /* ---------------------------------------------------------
         * 2. Procesar asistencia (por device_user_id)
         * ---------------------------------------------------------*/
        $this->info('Procesando asistencia...');
        $logs = DB::table('attendance_logs')
            ->whereDate('recorded_at', $date)
            ->get()
            ->groupBy('device_user_id');

        foreach ($logs as $deviceId => $items) {
            $processAction->execute($deviceId, $date, collect($items));
        }

        /* ---------------------------------------------------------
         * 3. Resolver resúmenes diarios (por vínculo laboral)
         * ---------------------------------------------------------*/
        $this->info('Resolviendo resumen diario...');

        $activeLinks = $laborLinks->allActive(); // Collection<SuanLaborLink>

        foreach ($activeLinks as $link) {
            $summaryAction->execute($link->id, $date);
        }

        $this->info("Proceso completo para $date finalizado.");

        return self::SUCCESS;
    }
}
