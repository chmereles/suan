<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\Repositories\EmployeeRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function index(EmployeeRepositoryInterface $repo)
    {
        return response()->json([
            'employees' => $repo->allActive(),
        ]);
    }

    public function mapDeviceUserId(
        Request $request,
        EmployeeRepositoryInterface $repo
    ) {
        $data = $request->validate([
            'legajo' => 'required|string',
            'device_user_id' => 'required|string',
        ]);

        $employee = $repo->findByLegajo($data['legajo']);

        if (! $employee) {
            return response()->json([
                'error' => 'Empleado no encontrado',
            ], 404);
        }

        $employee->update([
            'device_user_id' => $data['device_user_id'],
        ]);

        return response()->json([
            'success' => true,
            'employee' => $employee,
        ]);
    }
}
