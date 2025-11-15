<?php

namespace App\Infrastructure\Attendance\Persistence;

use App\Domain\Attendance\Models\SuanManualNote;
use App\Domain\Attendance\Repositories\ManualNoteRepositoryInterface;

class EloquentManualNoteRepository implements ManualNoteRepositoryInterface
{
    public function notesForEmployeeAndDate(int $employeeId, string $date): iterable
    {
        return SuanManualNote::where('employee_id', $employeeId)
            ->where('date', $date)
            ->get();
    }

    public function create(array $data): SuanManualNote
    {
        return SuanManualNote::create($data);
    }
}
