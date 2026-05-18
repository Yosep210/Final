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
        if (DB::table('hamlets')->where('village_id', $village->id)->exists()) {
            throw ValidationException::withMessages([
                'village' => 'Village cannot be deleted because it is already used by hamlet data.',
            ]);
        }

        return $village->delete();
    }
}
