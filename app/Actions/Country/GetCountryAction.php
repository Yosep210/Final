<?php

namespace App\Actions\Country;

use App\Models\Country;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetCountryAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Country::query())
            ->defaultSort('-id')
            ->allowedFilters(
                AllowedFilter::partial('iso'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('nice_name'),
                AllowedFilter::partial('iso3'),
                AllowedFilter::exact('numcode'),
                AllowedFilter::exact('phonecode'),
                AllowedFilter::exact('status'),
            )
            ->allowedSorts(
                'id',
                'iso',
                'name',
                'nice_name',
                'iso3',
                'numcode',
                'phonecode',
                'status',
            )
            ->paginate($perPage);
    }
}
