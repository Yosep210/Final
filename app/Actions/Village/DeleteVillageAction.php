<?php

namespace App\Actions\Village;

use App\Models\Village;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteVillageAction
{
    use AsAction;

    public function handle(Village $village): ?bool
    {
        if ($village->memberProfiles()->exists()) {
            throw ValidationException::withMessages([
                'village' => 'Village cannot be deleted because it is still used by member profiles.',
            ]);
        }

        return $village->delete();
    }
}
