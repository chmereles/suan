<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ProcessAttendanceRecordsAction;
use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use Illuminate\Console\Command;

class ResolveDailyCommand extends Command
{
    protected $signature = 'suan:resolve-daily {--date=}';

    protected $description = 'Procesa y resuelve la asistencia completa de un día (attendance + summary)';

    public function __construct(
        private EmployeeRepositoryInterface $employees,
        private ProcessAttendanceRecordsAction $processAttendance,
        private ResolveDailySummaryAction $resolveSummary
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $date = $this->option('date') ?? now()->toDateString();

        $this->info("== SUAN :: Procesando día $date ==");

        $employees = $this->employees->allMapped(); // método que ya estamos usando
        $count = count($employees);

        $this->info("Empleados encontrados: $count");

        foreach ($employees as $employee) {

            $this->line("Procesando empleado [{$employee->id}] {$employee->full_name}");

            // 1. Procesar logs → suan_attendance_records
            // $this->processAttendance->execute($employee->device_user_id, $date);
            ($this->processAttendance)(
                $employee->device_user_id,
                $date
            );

            // 2. Resolver resumen diario → suan_daily_summary
            $this->resolveSummary->execute($employee->id, $date);
        }

        $this->info('✔ Día procesado correctamente.');

        return Command::SUCCESS;
    }
}
