<?php

namespace App\Domain\Attendance\Repositories;

use App\Domain\Attendance\Models\SuanManualNote;

interface ManualNoteRepositoryInterface
{
    public function notesForEmployeeAndDate(int $employeeId, string $date): iterable;

    public function create(array $data): SuanManualNote;
}
