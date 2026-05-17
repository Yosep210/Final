<?php

namespace App\Domain\Country\Actions;

use App\Domain\Country\Data\CountryData;
use App\Models\Country;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCountryAction
{
    use AsAction;

    /**
     * Create a new country record from the normalized data object.
     */
    public function handle(CountryData $data): Country
    {
        return Country::query()->create($data->toArray());
    }
}
