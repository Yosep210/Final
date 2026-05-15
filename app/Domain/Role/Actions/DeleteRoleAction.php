<?php

namespace App\Domain\Role\Actions;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteRoleAction
{
    public function execute(Role $role): ?bool
    {
        if (DB::table('users')->where('role_id', $role->id)->exists()) {
            throw ValidationException::withMessages([
                'role' => 'Role cannot be deleted because it is already used by user data.',
            ]);
        }

        return $role->delete();
    }
}
