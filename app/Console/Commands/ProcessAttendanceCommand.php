<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ProcessAttendanceAction;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessAttendanceCommand extends Command
{
    protected $signature = 'suan:process-attendance 
                            {date? : Fecha a procesar (YYYY-MM-DD)}';

    protected $description = 'Procesa los attendance_logs y genera suan_attendance_records.';

    public function handle(
        ProcessAttendanceAction $action,
        EmployeeRepositoryInterface $employeeRepo
    ) {
        $date = $this->argument('date')
            ?? Carbon::yesterday()->toDateString();

        $this->info("Procesando asistencias para fecha: $date");

        // Carga logs crudos desde attendance_logs
        $logs = DB::table('attendance_logs')
            ->whereDate('recorded_at', $date)
            ->get()
            ->groupBy('device_user_id');

        $processed = 0;

        foreach ($logs as $deviceId => $items) {
            $action->execute($deviceId, $date, collect($items));
            $processed++;
        }

        $this->info("Registros procesados para $date: $processed");

        return self::SUCCESS;
    }
}
