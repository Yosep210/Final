<?php

namespace App\Domain\Province\Actions;

use App\Domain\Province\Data\ProvinceData;
use App\Models\Province;

class UpdateProvinceAction
{
    /**
     * Execute the action to update a province.
     */
    public function execute(Province $province, ProvinceData $data): Province
    {
        $province->fill($data->toArray());
        $province->save();

        return $province->refresh();
    }
}
