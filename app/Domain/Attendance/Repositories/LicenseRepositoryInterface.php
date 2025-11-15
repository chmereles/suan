<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanLicense;

interface LicenseRepositoryInterface
{
    public function findForEmployeeAndDate(int $employeeId, string $date): ?SuanLicense;

    public function createOrUpdate(array $data): SuanLicense;
}
