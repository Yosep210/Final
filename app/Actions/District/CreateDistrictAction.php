<?php

namespace App\Actions\District;

use App\Data\DistrictData;
use App\Models\District;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateDistrictAction
{
    use AsAction;

    public function handle(DistrictData $data): District
    {
        return District::query()->create($data->toArray());
    }
}
