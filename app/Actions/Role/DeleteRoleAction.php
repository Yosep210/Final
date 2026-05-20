<?php

namespace App\Actions\Role;

use App\Models\Role;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteRoleAction
{
    use AsAction;

    public function handle(Role $role): ?bool
    {
        return $role->delete();
    }
}
