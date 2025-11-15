<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Actions\ProcessAttendanceAction;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * GET /attendance/records?date=YYYY-MM-DD
     * Lista los registros procesados del día.
     */
    public function index(Request $request, AttendanceRecordRepositoryInterface $attendanceRepo)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        return response()->json([
            'date' => $date,
            'records' => $attendanceRepo->between($date, $date, employeeId: 0),
        ]);
    }

    /**
     * POST /attendance/process?date=YYYY-MM-DD
     * Procesa todas las fichadas del día.
     */
    public function process(
        Request $request,
        ProcessAttendanceAction $action
    ) {
        $date = $request->query('date', Carbon::today()->toDateString());

        // Logs crudos
        $logs = DB::table('attendance_logs')
            ->whereDate('recorded_at', $date)
            ->get()
            ->groupBy('device_user_id');

        $processed = 0;

        foreach ($logs as $deviceId => $items) {
            $action->execute($deviceId, $date, collect($items));
            $processed++;
        }

        return response()->json([
            'success' => true,
            'date' => $date,
            'processed' => $processed,
        ]);
    }
}
