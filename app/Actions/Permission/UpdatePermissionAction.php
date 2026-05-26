<?php

namespace App\Actions\Permission;

use App\Data\PermissionData;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Permission;

class UpdatePermissionAction
{
    use AsAction;

    public function handle(Permission $permission, PermissionData $data): Permission
    {
        $permission->fill($data->toArray());
        $permission->save();

        return $permission->refresh();
    }
}
