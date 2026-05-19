<?php

namespace App\Actions\Permission;

use App\Data\PermissionData;
use App\Models\Permission;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePermissionAction
{
    use AsAction;

    public function handle(PermissionData $data): Permission
    {
        return Permission::query()->create($data->toArray());
    }
}
