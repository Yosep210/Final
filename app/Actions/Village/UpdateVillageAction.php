<?php

namespace App\Actions\Village;

use App\Data\VillageData;
use App\Models\Village;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateVillageAction
{
    use AsAction;

    public function handle(Village $village, VillageData $villageData): Village
    {
        $village->fill($villageData->toArray());
        $village->save();

        return $village->refresh();
    }
}
