<?php

namespace App\Actions\Role;

use App\Data\RoleData;
use App\Models\Role;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateRoleAction
{
    use AsAction;

    public function handle(Role $role, RoleData $data): Role
    {
        $role->fill($data->toArray());
        $role->save();

        return $role->refresh();
    }
}
