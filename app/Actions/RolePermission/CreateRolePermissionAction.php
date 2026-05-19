<?php

namespace App\Actions\RolePermission;

use App\Data\RolePermissionData;
use App\Models\RolePermission;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateRolePermissionAction
{
    use AsAction;

    public function handle(RolePermissionData $data): RolePermission
    {
        return RolePermission::query()->create($data->toArray());
    }
}
