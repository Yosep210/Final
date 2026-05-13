<?php

namespace App\Domain\Country\Actions;

use App\Domain\Country\Data\CountryData;
use App\Models\Country;

class UpdateCountryAction
{
    /**
     * Execute the action to update a country.
     */
    public function execute(Country $country, CountryData $data): Country
    {
        $country->update($data->toArray());

        return $country;
    }
}
