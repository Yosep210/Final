<?php

namespace App\Actions\District;

use App\Data\DistrictData;
use App\Models\District;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateDistrictAction
{
    use AsAction;

    public function handle(DistrictData $data, District $district): District
    {
        $district->fill($data->toArray());
        $district->save();

        return $district->refresh();
    }
}
