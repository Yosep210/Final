<?php

namespace App\Domain\Province\Actions;

use App\Domain\Province\Data\ProvinceData;
use App\Models\Province;

class CreateProvinceAction
{
    /**
     * Execute the action to create a province.
     */
    public function execute(ProvinceData $data): Province
    {
        return Province::query()->create($data->toArray());
    }
}
