<?php

namespace App\Actions\Role;

use App\Data\RoleData;
use App\Models\Role;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateRoleAction
{
    use AsAction;

    public function handle(RoleData $data): Role
    {
        return Role::query()->create($data->toArray());
    }
}
