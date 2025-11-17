<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ProcessAttendanceAction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessAttendanceCommand extends Command
{
    protected $signature = 'suan:process-attendance 
                            {date? : Fecha a procesar (YYYY-MM-DD)}';

    protected $description = 'Procesa los attendance_logs y genera suan_attendance_records.';

    public function handle(ProcessAttendanceAction $action)
    {
        $date = $this->argument('date')
            ?? Carbon::yesterday()->toDateString();

        $this->info("Procesando asistencias para fecha: $date");

        // 1. Cargar logs crudos desde attendance_logs
        $logs = DB::table('attendance_logs')
            ->whereDate('recorded_at', $date)
            ->get()
            ->groupBy('device_user_id');

        $processed = 0;

        // 2. Por cada device_user_id, ejecutar acción
        foreach ($logs as $deviceId => $items) {

            $action->execute(
                deviceUserId: $deviceId,
                date: $date,
                logs: collect($items)
            );

            $processed++;
        }

        $this->info("Procesados: $processed device_user_id para $date");

        return self::SUCCESS;
    }
}
