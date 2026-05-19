<?php

namespace App\Actions\RolePermission;

use App\Models\RolePermission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetRolePermissionAction
{
    use AsAction;

    public function handle(int $perPage = 10): LengthAwarePaginator
    {
        return QueryBuilder::for(RolePermission::query())
            ->defaultSort('role_id')
            ->allowedFilters([
                AllowedFilter::exact('role_id'),
                AllowedFilter::exact('permission_id'),
            ])
            ->allowedSorts('role_id', 'permission_id')
            ->paginate($perPage);
    }
}
