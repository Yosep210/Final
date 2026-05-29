<?php

namespace App\Actions\City;

use App\Models\City;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetCityAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(City::query())
            ->defaultSort('-id')
            ->allowedFilters(
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
            )
            ->allowedSorts(
                'id',
                'name',
                'created_at'
            )
            ->paginate($perPage);
    }
}
