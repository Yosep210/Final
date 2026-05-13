<?php

namespace App\Domain\Country\Actions;

use App\Domain\Country\Data\CountryData;

class CreateCountryAction
{
    /**
     * Execute the action to create a country.
     */
    public function execute(CountryData $data): bool
    {
        // Logika bisnis di sini, contoh:
        // return Country::create($data->toArray());

        return true;
    }
}
