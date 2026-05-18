<?php

namespace App\Actions\City;

use App\Data\CityData;
use App\Models\City;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCityAction
{
    use AsAction;

    public function handle(City $city, CityData $cityData): City
    {
        $city->fill($cityData->toArray());
        $city->save();

        return $city->refresh();
    }
}
