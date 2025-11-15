<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Repositories\AnomalyRepositoryInterface;
use App\Domain\Attendance\Repositories\AttendanceRecordRepositoryInterface;
use App\Domain\Attendance\Repositories\DailySummaryRepositoryInterface;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;
use App\Domain\Attendance\Repositories\ManualNoteRepositoryInterface;

/**
 * Resuelve el estado final del día.
 */
class DailySummaryResolverService
{
    public function __construct(
        private AttendanceRecordRepositoryInterface $attendanceRepo,
        private DailySummaryRepositoryInterface $summaryRepo,
        private LicenseRepositoryInterface $licenseRepo,
        private ManualNoteRepositoryInterface $noteRepo,
        private AnomalyRepositoryInterface $anomalyRepo
    ) {}

    public function resolve(int $employeeId, string $date)
    {
        $attendance = $this->attendanceRepo->forEmployeeAndDate($employeeId, $date);
        $license = $this->licenseRepo->findForEmployeeAndDate($employeeId, $date);
        $notes = $this->noteRepo->notesForEmployeeAndDate($employeeId, $date);
        $anomalies = $this->anomalyRepo->unresolvedForEmployeeAndDate($employeeId, $date);

        $status = 'present';
        $worked = $attendance->worked_minutes ?? 0;
        $justified = false;
        $summaryNotes = null;

        // REGLAS

        if ($license) {
            $status = 'license';
        } elseif ($notes->count() > 0) {
            $status = 'absent_justified';
            $justified = true;
            $summaryNotes = $notes->pluck('content')->implode(' | ');
        } elseif (! $attendance || ! $attendance->check_in) {
            $status = 'absent_unjustified';
        }

        if ($anomalies->count() > 0) {
            $status = 'anomaly';
        }

        return $this->summaryRepo->createOrUpdate([
            'employee_id' => $employeeId,
            'date' => $date,
            'status' => $status,
            'total_worked_minutes' => $worked,
            'justified' => $justified,
            'notes' => $summaryNotes,
        ]);
    }
}
