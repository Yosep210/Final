<?php

namespace App\Actions\Province;

use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteProvinceAction
{
    use AsAction;

    public function handle(Province $province): ?bool
    {
        if (DB::table('cities')->where('province_id', $province->id)->exists()) {
            throw ValidationException::withMessages([
                'province' => 'Province cannot be deleted because it is already used by regency data.',
            ]);
        }

        return $province->delete();
    }
}
