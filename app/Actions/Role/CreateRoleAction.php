<?php

namespace App\Actions\Role;

use App\Data\RoleData;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    use AsAction;

    public function handle(RoleData $data): Role
    {
        return Role::query()->create($data->toArray());
    }
}
