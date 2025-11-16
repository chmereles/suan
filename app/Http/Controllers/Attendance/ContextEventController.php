<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Actions\RegisterContextEventAction;
use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContextEventController
{
    public function __construct(
        private RegisterContextEventAction $registerNote,
        private EmployeeRepositoryInterface $employeeRepo
    ) {}

    public function create(Request $request)
    {
       $employees = collect($this->employeeRepo->allActive())
            ->sortBy(fn($e) => $e->full_name)
            ->map(fn($e) => [
                'id'        => $e->id,
                'full_name' => $e->full_name,
                'legajo'    => $e->legajo,
                'area'      => $e->area,
            ])
            ->values()
            ->toArray();

        return Inertia::render('Attendance/ContextEvent/Create', [
            'date'       => $request->query('date', now()->toDateString()),
            'employeeId' => $request->query('employee_id'),
            'employees'  => $employees, // ← AQUI SE PASAN
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:suan_employees,id',
            'date'        => 'required|date',
            'type'        => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        ($this->registerNote)(
            employeeId:  (int) $validated['employee_id'],
            date:        $validated['date'],
            type:        $validated['type'],
            description: $validated['description'] ?? null,
            metadata:    ['source' => 'manual'],
            createdBy:   $request->user()?->id
        );

        return redirect()
            ->back()
            ->with('success', 'Nota externa registrada correctamente.');
    }
}
