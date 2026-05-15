<?php

namespace App\Domain\Role\Actions;

use App\Domain\Role\Data\RoleData;
use App\Models\Role;

class CreateRoleAction
{
    public function execute(RoleData $data): Role
    {
        return Role::query()->create($data->toArray());
    }
}
