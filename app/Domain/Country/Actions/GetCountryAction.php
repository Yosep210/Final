<?php

namespace App\Domain\Country\Actions;

use App\Models\Country;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCountryAction
{
    use AsAction;

    /**
     * Retrieve a paginated list of countries ordered by newest first.
     */
    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return Country::query()
            ->latest('id')
            ->paginate($perPage);
    }
}
