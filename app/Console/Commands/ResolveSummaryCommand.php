<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResolveSummaryCommand extends Command
{
    protected $signature = 'suan:resolve-summary 
                            {date? : Fecha a consolidar (YYYY-MM-DD)}';

    protected $description = 'Genera suan_daily_summary para cada empleado.';

    public function handle(
        ResolveDailySummaryAction $action,
        EmployeeRepositoryInterface $employeeRepo
    ) {
        $date = $this->argument('date')
            ?? Carbon::yesterday()->toDateString();

        $this->info("Resolviendo resumen diario para: $date");

        $employees = $employeeRepo->allActive();

        foreach ($employees as $employee) {
            $action->execute($employee->id, $date);
        }

        $this->info('Resumen diario resuelto para '.count($employees).' empleados.');

        return self::SUCCESS;
    }
}
