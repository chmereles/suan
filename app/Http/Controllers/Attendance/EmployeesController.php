<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Repositories\PersonRepositoryInterface;
use App\Domain\Attendance\Repositories\LaborLinkRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function index(
        PersonRepositoryInterface $people,
        LaborLinkRepositoryInterface $links
    ) {
        // Personas con vínculos laborales activos
        $persons = collect($people->all(withLaborLinks: true))
            ->map(function ($p) {
                return [
                    'person_id'   => $p->id,
                    'full_name'   => $p->full_name,
                    'document'    => $p->document,
                    'device_user_id' => $p->device_user_id,

                    'links' => $p->laborLinks
                        ->where('active', true)
                        ->map(fn ($l) => [
                            'id'        => $l->id,
                            'source'    => $l->source,
                            'external_id' => $l->external_id, // ← legajo legado
                            'area'      => $l->area,
                            'position'  => $l->position,
                        ])
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'people' => $persons,
        ]);
    }

    public function mapDeviceUserId(
        Request $request,
        PersonRepositoryInterface $people
    ) {
        $data = $request->validate([
            'document'        => 'required|string',
            'device_user_id'  => 'required|string',
        ]);

        // Buscar persona por documento
        $person = $people->findByDocument($data['document']);

        if (! $person) {
            return response()->json([
                'error' => 'Persona no encontrada',
            ], 404);
        }

        // Asignar device_user_id a la persona
        $person->update([
            'device_user_id' => $data['device_user_id'],
        ]);

        return response()->json([
            'success' => true,
            'person'  => $person,
        ]);
    }
}
