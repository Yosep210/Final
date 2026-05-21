<?php

namespace App\Actions\Member\Profile;

use App\Models\MemberProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GetProfileAction
{
    use AsAction;

    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(MemberProfile::query())
            ->defaultSort('-id')
            ->allowedFilters([
                AllowedFilter::exact('member_id'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('province_id'),
                AllowedFilter::exact('city_id'),
                AllowedFilter::exact('district_id'),
                AllowedFilter::exact('village_id'),
            ])
            ->allowedSorts(['id', 'member_id', 'country_id', 'province_id', 'city_id', 'district_id', 'village_id'])
            ->paginate($perPage);
    }
}
