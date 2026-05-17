<?php

namespace App\Actions\Province;

use App\Models\Province;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetProvinceAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(Province::query())
            ->defaultSort('-id')
            ->allowedFilters([
                AllowedFilter::partial('country_id'),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts('id', 'country_id', 'name')
            ->paginate($perPage);
    }
}
