<?php

namespace App\Actions\Province;

use App\Data\ProvinceData;
use App\Models\Province;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProvinceAction
{
    use AsAction;

    public function handle(Province $province, ProvinceData $data): Province
    {
        $province->fill($data->toArray());
        $province->save();

        return $province->refresh();
    }
}
