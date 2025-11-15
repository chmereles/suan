<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use App\Domain\Attendance\Services\AnomalyDetectorService;
use Carbon\Carbon;

class DailySummaryResolverService
{
    public function __construct(
        private AttendanceRecordRepositoryInterface $recordsRepo,
        private DailySummaryRepositoryInterface $summaryRepo,
        private LicenseRepositoryInterface $licenseRepo,
        private ContextEventRepositoryInterface $contextEventRepo,
        private AnomalyDetectorService $anomalyDetector
    ) {}

    /**
     * Resuelve el estado final del día para un empleado.
     *
     * @param int    $employeeId
     * @param string $date (YYYY-MM-DD)
     */
    public function resolve(int $employeeId, string $date)
    {
        // -----------------------------------------------------
        // 1) Obtener registros procesados del día
        // -----------------------------------------------------
        $records = $this->recordsRepo->getByEmployeeAndDate($employeeId, $date);

        // Fecha como Carbon
        $carbonDate = Carbon::parse($date);

        // -----------------------------------------------------
        // 2) Licencias del día
        // -----------------------------------------------------
        $hasLicense = $this->licenseRepo->hasLicenseForDate($employeeId, $carbonDate);

        // -----------------------------------------------------
        // 3) Eventos de contexto (justificaciones, comisiones, etc.)
        // -----------------------------------------------------
        $hasContextEvent = $this->contextEventRepo->hasEventForDate($employeeId, $carbonDate);

        // -----------------------------------------------------
        // 4) Si no hay registros y tiene licencia → RESUELTO
        // -----------------------------------------------------
        if (empty($records) && $hasLicense) {
            return $this->summaryRepo->storeOrUpdate($employeeId, $date, [
                'status' => 'license',
                'has_license' => true,
                'has_context_event' => $hasContextEvent,
                'worked_minutes' => 0,
                'anomalies' => [],
            ]);
        }

        // -----------------------------------------------------
        // 5) Si no hay registros y NO tiene licencia → ausencia
        // -----------------------------------------------------
        if (empty($records) && ! $hasContextEvent) {
            return $this->summaryRepo->storeOrUpdate($employeeId, $date, [
                'status' => 'absent_unjustified',
                'worked_minutes' => 0,
                'anomalies' => ['no_marks' => true],
            ]);
        }

        // -----------------------------------------------------
        // 6) Procesar check-in / check-out reales
        // -----------------------------------------------------
        $checkIn  = Carbon::parse($records[0]->recorded_at);
        $checkOut = isset($records[count($records) - 1])
            ? Carbon::parse($records[count($records) - 1]->recorded_at)
            : null;

        // worked minutes
        $workedMinutes = $checkOut
            ? $checkIn->diffInMinutes($checkOut)
            : 0;

        // -----------------------------------------------------
        // 7) Anomalías
        // -----------------------------------------------------
        $anomalies = $this->anomalyDetector->detect($records, $employeeId, $date);

        // Si hay anomalías fuertes → estado anomaly
        $status = empty($anomalies) ? 'present' : 'anomaly';

        // Eventos y licencias pueden modificar el estado
        if ($hasLicense) {
            $status = 'license';
        } elseif ($hasContextEvent) {
            $status = 'absent_justified';
        }

        // -----------------------------------------------------
        // 8) Guardar resumen diario
        // -----------------------------------------------------
        return $this->summaryRepo->storeOrUpdate($employeeId, $date, [
            'status' => $status,
            'has_license' => $hasLicense,
            'has_context_event' => $hasContextEvent,
            'worked_minutes' => $workedMinutes,
            'late_minutes' => $this->calculateLateMinutes($checkIn),
            'early_leave_minutes' => $this->calculateEarlyLeaveMinutes($checkOut),
            'anomalies' => $anomalies,
            'metadata' => [
                'record_count' => count($records),
                'records' => $records,
            ],
        ]);
    }


    private function calculateLateMinutes(Carbon $checkIn): int
    {
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

        $end = Carbon::parse($checkOut->toDateString().' 13:00:00');
        return $checkOut->lessThan($end)
            ? $end->diffInMinutes($checkOut)
            : 0;
    }
}
