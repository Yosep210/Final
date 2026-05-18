<?php

namespace App\Actions\Village;

use App\Data\VillageData;
use App\Models\Village;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateVillageAction
{
    use AsAction;

    public function handle(VillageData $villageData): Village
    {
        return Village::create($villageData->toArray());
    }
}
