<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Actions\RegisterContextEventAction;
use App\Domain\Attendance\Repositories\PersonRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContextEventController
{
    public function __construct(
        private RegisterContextEventAction $registerNote,
        private PersonRepositoryInterface $people,
        private LaborLinkRepositoryInterface $links
    ) {}

    public function create(Request $request)
    {
        // 1. Obtener todas las personas con device_user_id o activas (según tu regla)
        $persons = collect($this->people->all(withLaborLinks: true))
            ->map(function ($p) {
                return [
                    'person_id'  => $p->id,
                    'full_name'  => $p->full_name,
                    'document'   => $p->document,
                    'links'      => $p->laborLinks
                        ->where('active', true)
                        ->map(fn ($l) => [
                            'id'         => $l->id,          // ← labor_link_id
                            'source'     => $l->source,      // haberes / planes
                            'area'       => $l->area,
                            'position'   => $l->position,
                            'schedule'   => $l->schedule,
                        ])
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('Attendance/ContextEvent/Create', [
            'date'     => $request->query('date', now()->toDateString()),
            'linkId'   => $request->query('labor_link_id'),
            'persons'  => $persons,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'labor_link_id' => 'required|integer|exists:suan_labor_links,id',
            'date'          => 'required|date',
            'type'          => 'required|string|max:50',
            'description'   => 'nullable|string',
        ]);

        // 2. Registrar nota
        ($this->registerNote)(
            laborLinkId: (int) $validated['labor_link_id'],
            date:        $validated['date'],
            type:        $validated['type'],
            description: $validated['description'] ?? null,
            metadata:    ['source' => 'manual'],
            createdBy:   $request->user()?->id
        );

        return redirect()
            ->back()
            ->with('success', 'Nota registrada correctamente.');
    }
}
