<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;

class DepartmentPolicy
{
    /**
     * Determine whether the user can update the department.
     */
    public function update(?User $user, Department $department): bool
    {
        if (is_null($user)) return false;

        // If the User model includes a `role` attribute, honor it.
        $role = $user->role ?? $user->rol ?? null;
        if ($role && in_array(strtolower($role), ['admin', 'superadmin', 'administrator'])) {
            return true;
        }

        // Fallback: allow known demo admin emails
        $admins = [
            'admin@demo.com',
            'root@demo.com',
        ];

        if (in_array(strtolower($user->email), $admins)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the department.
     */
    public function delete(?User $user, Department $department): bool
    {
        return $this->update($user, $department);
    }
}
