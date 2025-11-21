<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Enums\AnomalyType;
use App\Domain\Attendance\Enums\DailyStatus;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;
use App\Domain\Labor\Services\ScheduleResolverService;
use Carbon\Carbon;

class DailySummaryResolverService
{
    public function __construct(
        private AttendanceRecordRepositoryInterface $recordsRepo,
        private DailySummaryRepositoryInterface $summaryRepo,
        private LicenseRepositoryInterface $licenseRepo,
        private ContextEventRepositoryInterface $contextEventRepo,
        private LaborLinkRepositoryInterface $linksRepo,
        private AnomalyDetectorService $anomalyDetector,
        private ScheduleResolverService $scheduleResolver, // 🔹 Nuevo: dominio Labor
    ) {}

    /**
     * Resuelve el estado final del día para un vínculo laboral.
     *
     * @param  string  $date  YYYY-MM-DD
     */
    public function resolve(int $laborLinkId, string $date)
    {
        $carbonDate = Carbon::parse($date);

        // ---------------------------------------------------------
        // 1) Cargar vínculo laboral (para conocer horario)
        // ---------------------------------------------------------
        $link = $this->linksRepo->findById($laborLinkId);
        $scheduleModel = $link->schedule ?? null;  // SuanSchedule o null

        // Ventanas esperadas de trabajo para el día (Labor)
        $workWindows = [];

        if ($scheduleModel) {
            $workWindows = $this->scheduleResolver->resolveFor(
                $scheduleModel,
                $carbonDate,
                $link->schedule_rotation_start_date ?? null
            );
        }

        // ---------------------------------------------------------
        // 2) Registros procesados del día
        // ---------------------------------------------------------
        $records = $this->recordsRepo->getByLaborLinkAndDate($laborLinkId, $date);

        // ---------------------------------------------------------
        // 3) Licencias del día
        // ---------------------------------------------------------
        $hasLicense = $this->licenseRepo->hasLicenseForDate($laborLinkId, $carbonDate);

        // ---------------------------------------------------------
        // 4) Eventos de contexto
        // ---------------------------------------------------------
        $hasContextEvent = $this->contextEventRepo->hasEventForDate($laborLinkId, $carbonDate);

        // ---------------------------------------------------------
        // 5) Si no hay registros y tiene licencia → licencia
        // ---------------------------------------------------------
        if (empty($records) && $hasLicense) {
            return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
                'status' => DailyStatus::LICENSE->value,
                'has_license' => true,
                'has_context_event' => $hasContextEvent,
                'worked_minutes' => 0,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'anomalies' => [],
            ]);
        }

        // ---------------------------------------------------------
        // 6) Si no hay registros y no está justificado → ausente injustificado
        // ---------------------------------------------------------
        if (empty($records) && ! $hasContextEvent) {
            $status = DailyStatus::ABSENT_UNJUSTIFIED;

            return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
                'status' => $status->value,
                'has_license' => $hasLicense,
                'has_context_event' => false,
                'worked_minutes' => 0,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'anomalies' => [AnomalyType::NO_MARKS->value => true],
            ]);
        }

        // ---------------------------------------------------------
        // 6.b) Si no hay registros pero SÍ hay evento de contexto → ausente justificado
        // ---------------------------------------------------------
        if (empty($records) && $hasContextEvent) {
            $status = DailyStatus::ABSENT_JUSTIFIED;

            return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
                'status' => $status->value,
                'has_license' => false,
                'has_context_event' => true,
                'worked_minutes' => 0,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'anomalies' => [],
                'metadata' => [
                    'reason' => 'context_event',
                ],
            ]);
        }

        // ---------------------------------------------------------
        // 7) Interpretar Check-In / Check-Out (por ahora: primero y último)
        // ---------------------------------------------------------
        $checkIn = Carbon::parse($records[0]->recorded_at);
        $checkOut = isset($records[count($records) - 1])
            ? Carbon::parse($records[count($records) - 1]->recorded_at)
            : null;

        $workedMinutes = $checkOut
            ? $checkIn->diffInMinutes($checkOut)
            : 0;

        // ---------------------------------------------------------
        // 8) Calcular anomalías
        // ---------------------------------------------------------
        $anomalies = $this->anomalyDetector->detect($records, $laborLinkId, $date);

        $status = empty($anomalies)
            ? DailyStatus::PRESENT
            : DailyStatus::ANOMALY;

        if ($hasLicense) {
            $status = DailyStatus::LICENSE;
        } elseif ($hasContextEvent && empty($records)) {
            // ya cubierto antes, pero lo dejamos claro
            $status = DailyStatus::ABSENT_JUSTIFIED;
        }

        // ---------------------------------------------------------
        // 9) Calcular late / early usando ventanas de horario (Labor)
        // ---------------------------------------------------------
        $lateMinutes  = $this->calculateLateMinutes($checkIn, $workWindows);
        $earlyLeaveMinutes  = $this->calculateEarlyLeaveMinutes($checkOut, $workWindows);

        // ---------------------------------------------------------
        // 10) Guardar resumen final
        // ---------------------------------------------------------
        return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
            'status' => $status->value,
            'has_license' => $hasLicense,
            'has_context_event' => $hasContextEvent,
            'worked_minutes' => $workedMinutes,
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
            'anomalies' => $anomalies,
            'metadata' => [
                'record_count' => count($records),
                // Ojo: acá quizá en producción quieras NO guardar todos los records crudos
                'records' => $records,
            ],
        ]);
    }

    /**
     * @param  array<int, array{start: Carbon, end: Carbon, tolerance_in: int, tolerance_out: int}>  $workWindows
     */
    private function calculateLateMinutes(Carbon $checkIn, array $workWindows): int
    {
        if (empty($workWindows)) {
            return 0;
        }

        // Por ahora tomamos el primer tramo como referencia
        $first = $workWindows[0];

        $allowedStart = $first['start']->copy()->addMinutes($first['tolerance_in']);

        if ($checkIn->lessThanOrEqualTo($allowedStart)) {
            return 0;
        }

        return $allowedStart->diffInMinutes($checkIn);
    }

    /**
     * @param  array<int, array{start: Carbon, end: Carbon, tolerance_in: int, tolerance_out: int}>  $workWindows
     */
    private function calculateEarlyLeaveMinutes(?Carbon $checkOut, array $workWindows): int
    {
        if (! $checkOut || empty($workWindows)) {
            return 0;
        }

        // Tomamos el último tramo como referencia
        $last = $workWindows[count($workWindows) - 1];

        $expectedEnd = $last['end']->copy()->subMinutes($last['tolerance_out']);

        if ($checkOut->greaterThanOrEqualTo($expectedEnd)) {
            return 0;
        }

        return $expectedEnd->diffInMinutes($checkOut);
    }
}
