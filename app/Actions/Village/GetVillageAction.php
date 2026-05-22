<?php

namespace App\Actions\Village;

use App\Models\Village;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetVillageAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Village::query())
            ->defaultSort('-id')
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('postal_code'),
            ])
            ->allowedSorts('id', 'name', 'postal_code')
            ->paginate($perPage);
    }
}
