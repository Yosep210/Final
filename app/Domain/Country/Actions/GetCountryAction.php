<?php

namespace App\Domain\Country\Actions;

use App\Models\Country;

class GetCountryAction
{
    /**
     * Execute the action to retrieve a country by its ID.
     */
    public function execute(int $countryId): ?Country
    {
        return Country::find($countryId);
    }
}
