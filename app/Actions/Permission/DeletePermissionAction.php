<?php

namespace App\Actions\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;

class DeletePermissionAction
{
    use AsAction;

    public function handle(Permission $permission): ?bool
    {
        if (DB::table('role_has_permissions')->where('permission_id', $permission->id)->exists()) {
            throw ValidationException::withMessages([
                'permission' => 'Permission cannot be deleted because it is already used by role data.',
            ]);
        }

        return $permission->delete();
    }
}
