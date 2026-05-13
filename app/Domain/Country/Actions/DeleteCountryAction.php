<?php

namespace App\Domain\Country\Actions;

use App\Models\Country;

class DeleteCountryAction
{
    /**
     * Execute the action to delete a country.
     */
    public function execute(Country $country): ?bool
    {
        return $country->delete();
    }
}
