<?php

namespace App\Domain\Role\Actions;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetRoleAction
{
    /**
     * Execute the action to retrieve a paginated list of roles.
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Role::query()
            ->latest('id')
            ->paginate($perPage);
    }
}
