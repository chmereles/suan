<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Enums\AnomalyType;
use App\Domain\Attendance\Enums\DailyStatus;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;
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
        $schedule = $link->schedule ?? null;  // {"start":"08:00","end":"12:00"}

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
                'anomalies' => [],
            ]);
        }

        // ---------------------------------------------------------
        // 6) Si no hay registros y no está justificado → ausente
        // ---------------------------------------------------------
        if (empty($records) && ! $hasContextEvent) {
            $status = DailyStatus::ABSENT_UNJUSTIFIED->value;

            return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
                'status' => $status,
                'worked_minutes' => 0,
                'anomalies' => [AnomalyType::NO_MARKS->value => true],
            ]);
        }

        // ---------------------------------------------------------
        // 6.b) Si no hay registros pero SÍ hay evento de contexto → ausente justificado
        // ---------------------------------------------------------
        if (empty($records) && $hasContextEvent) {
            return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
                'status' => DailyStatus::ABSENT_JUSTIFIED->value,
                'has_license' => false,
                'has_context_event' => true,
                'worked_minutes' => 0,
                'anomalies' => [],
                'metadata' => [
                    'reason' => 'context_event',
                ],
            ]);
        }

        // ---------------------------------------------------------
        // 7) Interpretar Check-In / Check-Out
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

        $status = empty($anomalies) ? DailyStatus::PRESENT : DailyStatus::ANOMALY;

        if ($hasLicense) {
            $status = DailyStatus::LICENSE;
        } elseif ($hasContextEvent) {
            $status = DailyStatus::ABSENT_JUSTIFIED;
        }

        // ---------------------------------------------------------
        // 9) Guardar resumen final
        // ---------------------------------------------------------
        $late_minutes  = $this->calculateLateMinutes($checkIn, $schedule);
        $early_leave_minutes  = $this->calculateEarlyLeaveMinutes($checkOut, $schedule);

        return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
            'status' => $status->value,
            'has_license' => $hasLicense,
            'has_context_event' => $hasContextEvent,
            'worked_minutes' => $workedMinutes,
            'late_minutes' => $late_minutes,
            'early_leave_minutes' => $early_leave_minutes,
            'anomalies' => $anomalies,
            'metadata' => [
                'record_count' => count($records),
                'records' => $records,
            ],
        ]);
    }

    private function calculateLateMinutes(Carbon $checkIn, ?array $schedule): int
    {
        if (! $schedule) {
            return 0;
        }

        $start = Carbon::parse($checkIn->toDateString().' '.$schedule['start']);

        $data = $checkIn->greaterThan($start)
            ? $start->diffInMinutes($checkIn)
            : 0;

        return $data;
    }

    private function calculateEarlyLeaveMinutes(?Carbon $checkOut, ?array $schedule): int
    {
        if (! $checkOut || ! $schedule) {
            return 0;
        }

        $end = Carbon::parse($checkOut->toDateString().' '.$schedule['end']);

        $data = $checkOut->lessThan($end)
            ? $end->diffInMinutes($checkOut)
            : 0;
            
        return abs($data);
    }
}
