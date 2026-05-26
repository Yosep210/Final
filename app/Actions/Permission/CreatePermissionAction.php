<?php

namespace App\Actions\Permission;

use App\Data\PermissionData;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;

class CreatePermissionAction
{
    use AsAction;

    public function handle(PermissionData $data): Permission
    {
        return Permission::query()->create($data->toArray());
    }
}
