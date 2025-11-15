<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanLicense;
use App\Domain\Attendance\Repositories\LicenseRepositoryInterface;

class EloquentLicenseRepository implements LicenseRepositoryInterface
{
    public function findForEmployeeAndDate(int $employeeId, string $date): ?SuanLicense
    {
        return SuanLicense::where('employee_id', $employeeId)
            ->where('date', $date)
            ->first();
    }

    public function createOrUpdate(array $data): SuanLicense
    {
        return SuanLicense::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'date' => $data['date'],
            ],
            $data
        );
    }
}
