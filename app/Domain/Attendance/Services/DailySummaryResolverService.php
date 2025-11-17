<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;
use App\Domain\Attendance\Repositories\ContextEventRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
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
     * @param int    $laborLinkId
     * @param string $date        YYYY-MM-DD
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
                'status'            => 'license',
                'has_license'       => true,
                'has_context_event' => $hasContextEvent,
                'worked_minutes'    => 0,
                'anomalies'         => [],
            ]);
        }

        // ---------------------------------------------------------
        // 6) Si no hay registros y no está justificado → ausente
        // ---------------------------------------------------------
        if (empty($records) && ! $hasContextEvent) {
            return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
                'status'            => 'absent_unjustified',
                'worked_minutes'    => 0,
                'anomalies'         => ['no_marks' => true],
            ]);
        }

        // ---------------------------------------------------------
        // 7) Interpretar Check-In / Check-Out
        // ---------------------------------------------------------
        $checkIn  = Carbon::parse($records[0]->recorded_at);
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

        $status = empty($anomalies) ? 'present' : 'anomaly';

        if ($hasLicense) {
            $status = 'license';
        } elseif ($hasContextEvent) {
            $status = 'absent_justified';
        }

        // ---------------------------------------------------------
        // 9) Guardar resumen final
        // ---------------------------------------------------------
        return $this->summaryRepo->storeOrUpdate($laborLinkId, $date, [
            'status'              => $status,
            'has_license'         => $hasLicense,
            'has_context_event'   => $hasContextEvent,
            'worked_minutes'      => $workedMinutes,
            'late_minutes'        => $this->calculateLateMinutes($checkIn, $schedule),
            'early_leave_minutes' => $this->calculateEarlyLeaveMinutes($checkOut, $schedule),
            'anomalies'           => $anomalies,
            'metadata'            => [
                'record_count' => count($records),
                'records'      => $records,
            ],
        ]);
    }

    private function calculateLateMinutes(Carbon $checkIn, ?array $schedule): int
    {
        if (! $schedule) return 0;

        $start = Carbon::parse($checkIn->toDateString() . ' ' . $schedule['start']);

        return $checkIn->greaterThan($start)
            ? $start->diffInMinutes($checkIn)
            : 0;
    }

    private function calculateEarlyLeaveMinutes(?Carbon $checkOut, ?array $schedule): int
    {
        if (! $checkOut || ! $schedule) return 0;

        $end = Carbon::parse($checkOut->toDateString() . ' ' . $schedule['end']);

        return $checkOut->lessThan($end)
            ? $end->diffInMinutes($checkOut)
            : 0;
    }
}
