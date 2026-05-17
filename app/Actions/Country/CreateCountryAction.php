<?php

namespace App\Actions\Country;

use App\Data\CountryData;
use App\Models\Country;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCountryAction
{
    use AsAction;

    public function handle(CountryData $data): Country
    {
        return Country::query()->create($data->toArray());
    }
}
