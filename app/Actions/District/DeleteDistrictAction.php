<?php

namespace App\Actions\District;

use App\Models\District;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteDistrictAction
{
    use AsAction;

    public function handle(District $district): ?bool
    {
        if (DB::table('villages')->where('district_id', $district->id)->exists()) {
            throw ValidationException::withMessages([
                'district' => 'District cannot be deleted because it is already used by village data.',
            ]);
        }

        return $district->delete();
    }
}
