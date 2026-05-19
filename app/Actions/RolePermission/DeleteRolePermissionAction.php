<?php

namespace App\Actions\RolePermission;

use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteRolePermissionAction
{
    use AsAction;

    public function handle(RolePermission $rolePermission): ?bool
    {
        if (! DB::table('role_has_permissions')->where('role_id', $rolePermission->role_id)->where('permission_id', $rolePermission->permission_id)->exists()) {
            throw ValidationException::withMessages(['role_permission' => ['This role permission combination does not exist.']]);
        }

        return $rolePermission->delete();
    }
}
