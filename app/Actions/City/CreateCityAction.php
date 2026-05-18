<?php

namespace App\Actions\City;

use App\Data\CityData;
use App\Models\City;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCityAction
{
    use AsAction;

    public function handle(CityData $cityData): City
    {
        return City::create($cityData->toArray());
    }
}
