<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Models\SuanAttendanceRecord;
use App\Domain\Attendance\Repositories\AnomalyRepositoryInterface;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Procesa registros brutos (attendance_logs) y genera:
 * - suan_attendance_records
 * - anomalías (sin salida, marcaciones duplicadas, etc.)
 */
class AttendanceProcessorService
{
    public function __construct(
        private AttendanceRecordRepositoryInterface $attendanceRepo,
        private EmployeeRepositoryInterface $employeeRepo,
        private AnomalyRepositoryInterface $anomalyRepo
    ) {}

    /**
     * Procesa fichadas para un empleado en una fecha
     *
     * @param  Collection  $logs  → logs crudos filtrados por empleado + fecha
     * @param  string  $date  YYYY-MM-DD
     */
    public function processEmployeeLogs(Collection $logs, int $employeeId, string $date): SuanAttendanceRecord
    {
        if ($logs->isEmpty()) {
            // Si no hay logs, no creamos un record aquí
            // Será el DailySummary quien determine ausencia
            return $this->attendanceRepo->forEmployeeAndDate($employeeId, $date)
                ?? new SuanAttendanceRecord;
        }

        // Ordenar por fecha/hora por seguridad
        $logs = $logs->sortBy('recorded_at')->values();

        // Primera marcación = entrada
        $checkIn = Carbon::parse($logs->first()->recorded_at);

        // Última marcación = salida
        $checkOut = $logs->count() > 1
            ? Carbon::parse($logs->last()->recorded_at)
            : null;

        // Detectar anomalía: no hay salida
        if (! $checkOut) {
            $this->anomalyRepo->create([
                'employee_id' => $employeeId,
                'date' => $date,
                'anomaly_type' => 'missing_checkout',
                'description' => 'Falta registro de salida.',
            ]);
        }

        // Calcular minutos trabajados
        $workedMinutes = $checkOut
            ? $checkIn->diffInMinutes($checkOut)
            : 0;

        // Reglas básicas de tardanza y salida anticipada
        $lateMinutes = max(0, $this->calculateLateMinutes($checkIn));
        $earlyLeaveMins = max(0, $this->calculateEarlyLeaveMinutes($checkOut));

        $data = [
            'employee_id' => $employeeId,
            'date' => $date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'worked_minutes' => $workedMinutes,
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMins,
            'metadata' => [
                'raw_count' => $logs->count(),
                'records' => $logs->toArray(),
            ],
        ];

        // Crear o actualizar registro del día
        $existing = $this->attendanceRepo->forEmployeeAndDate($employeeId, $date);

        return $existing
            ? $this->attendanceRepo->update($existing, $data)
            : $this->attendanceRepo->create($data);
    }

    private function calculateLateMinutes(Carbon $checkIn): int
    {
        // Ejemplo: jornada inicia a las 07:00
        $start = Carbon::parse($checkIn->toDateString().' 07:00:00');

        return $checkIn->greaterThan($start)
            ? $start->diffInMinutes($checkIn)
            : 0;
    }

    private function calculateEarlyLeaveMinutes(?Carbon $checkOut): int
    {
        if (! $checkOut) {
            return 0;
        }

        // Ejemplo: jornada termina a las 13:00
        $end = Carbon::parse($checkOut->toDateString().' 13:00:00');

        return $checkOut->lessThan($end)
            ? $end->diffInMinutes($checkOut)
            : 0;
    }
}
