<?php

namespace App\Domain\Province\Actions;

use App\Models\Province;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetProvinceAction
{
    /**
     * Execute the action to retrieve paginated list of provinces.
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return Province::query()
            ->latest('id')
            ->paginate($perPage);
    }
}
