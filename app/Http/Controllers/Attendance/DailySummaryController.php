<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Actions\ResolveDailySummaryAction;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailySummaryController extends Controller
{
    /**
     * GET /attendance/summary?date=YYYY-MM-DD
     * Devuelve resúmenes diarios.
     */
    public function index(Request $request, DailySummaryRepositoryInterface $summaryRepo)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $summaries = $summaryRepo->forRange($date, $date);

        return response()->json([
            'date' => $date,
            'summaries' => $summaries,
        ]);
    }

    /**
     * POST /attendance/summary/resolve?date=YYYY-MM-DD
     * Ejecuta el consolidado del día.
     */
    public function resolve(
        Request $request,
        EmployeeRepositoryInterface $employeeRepo,
        ResolveDailySummaryAction $action
    ) {
        $date = $request->query('date', Carbon::today()->toDateString());

        $employees = $employeeRepo->allActive();

        foreach ($employees as $employee) {
            $action->execute($employee->id, $date);
        }

        return response()->json([
            'success' => true,
            'date' => $date,
            'count' => count($employees),
        ]);
    }
}
