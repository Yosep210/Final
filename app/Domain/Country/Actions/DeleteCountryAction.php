<?php

namespace App\Domain\Country\Actions;

use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCountryAction
{
    use AsAction;

    /**
     * Delete a country if it is not referenced by province data.
     */
    public function handle(Country $country): ?bool
    {
        if (DB::table('provincies')->where('country_id', $country->id)->exists()) {
            throw ValidationException::withMessages([
                'country' => 'Country cannot be deleted because it is already used by province data.',
            ]);
        }

        return $country->delete();
    }
}
