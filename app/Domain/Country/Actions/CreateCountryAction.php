<?php

namespace App\Domain\Country\Actions;

use App\Domain\Country\Data\CountryData;
use App\Models\Country;

class CreateCountryAction
{
    /**
     * Execute the action to create a country.
     */
    public function execute(CountryData $data): Country
    {
        return Country::query()->create($data->toArray());
    }
}
