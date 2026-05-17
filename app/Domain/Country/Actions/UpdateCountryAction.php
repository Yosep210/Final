<?php

namespace App\Domain\Country\Actions;

use App\Domain\Country\Data\CountryData;
use App\Models\Country;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCountryAction
{
    use AsAction;

    /**
     * Update an existing country with normalized data.
     */
    public function handle(Country $country, CountryData $data): Country
    {
        $country->fill($data->toArray());
        $country->save();

        return $country->refresh();
    }
}
