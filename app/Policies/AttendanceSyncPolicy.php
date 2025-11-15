<?php

namespace App\Policies;

use App\Models\User;

class AttendanceSyncPolicy
{
    public function run(User $user): bool
    {
        // POR AHORA, permitir a todos los administradores:
        return $user->is_admin ?? true;
    }
}
