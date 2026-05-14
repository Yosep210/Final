<?php

namespace App\Domain\Province\Actions;

use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteProvinceAction
{
    /**
     * Execute the action to delete a province.
     */
    public function execute(Province $province): bool
    {
        if (DB::table('provincies')->where('province_id', $province->id)->exists()) {
            throw ValidationException::withMessages([
                'province' => 'Province cannot be deleted because it is already used by other data.',
            ]);
        }

        return $province->delete();
    }
}
