<?php

namespace App\Actions\District;

use App\Models\District;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetDistrictAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(District::query())
            ->defaultSort('-id')
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::exact('city_id'),
            )
            ->allowedSorts(
                'id',
                'name',
                'city_id',
            )
            ->paginate($perPage);
    }
}
