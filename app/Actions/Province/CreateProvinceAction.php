<?php

namespace App\Actions\Province;

use App\Data\ProvinceData;
use App\Models\Province;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateProvinceAction
{
    use AsAction;

    public function handle(ProvinceData $data): Province
    {
        return Province::query()->create($data->toArray());
    }
}
