<?php

namespace App\Actions\Country;

use App\Data\CountryData;
use App\Models\Country;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCountryAction
{
    use AsAction;

    public function handle(Country $country, CountryData $data): Country
    {
        $country->fill($data->toArray());
        $country->save();

        return $country->refresh();
    }
}
