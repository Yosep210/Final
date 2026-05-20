<?php

namespace App\Actions\Member;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetMemberAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Member::query())
            ->defaultSort('-id')
            ->allowedFilters(
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('username'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('status'),
            )
            ->allowedSorts('id', 'name', 'username', 'email', 'status', 'created_at')
            ->paginate($perPage);
    }
}
