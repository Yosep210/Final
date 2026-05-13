<?php

namespace App\Domain\Country\Actions;

use App\Models\Country;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCountryAction
{
    /**
     * Execute the action to retrieve a paginated list of countries.
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Country::query()
            ->latest('id')
            ->paginate($perPage);
    }
}
