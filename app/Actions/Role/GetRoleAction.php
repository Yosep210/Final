<?php

namespace App\Actions\Role;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetRoleAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Role::query())
            ->defaultSort('-id')
            ->allowedFilters(
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('guard_name'),
            )
            ->allowedSorts('id', 'name', 'guard_name')
            ->paginate($perPage);
    }
}
