<?php

namespace App\Actions\RolePermission;

use App\Data\RolePermissionData;
use App\Models\RolePermission;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateRolePermissionAction
{
    use AsAction;

    public function handle(RolePermission $rolePermission, RolePermissionData $data): RolePermission
    {
        $rolePermission->fill($data->toArray());
        $rolePermission->save();

        return $rolePermission;
    }
}
