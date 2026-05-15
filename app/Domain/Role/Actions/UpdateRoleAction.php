<?php

namespace App\Domain\Role\Actions;

use App\Domain\Role\Data\RoleData;
use App\Models\Role;

class UpdateRoleAction
{
    public function execute(Role $role, RoleData $data): Role
    {
        $role->fill($data->toArray());
        $role->save();

        return $role->refresh();
    }
}
