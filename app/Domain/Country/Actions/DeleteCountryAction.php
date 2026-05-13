<?php

namespace App\Domain\Country\Actions;

use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteCountryAction
{
    /**
     * Execute the action to delete a country.
     */
    public function execute(Country $country): ?bool
    {
        if (DB::table('provincies')->where('country_id', $country->id)->exists()) {
            throw ValidationException::withMessages([
                'country' => 'Country cannot be deleted because it is already used by province data.',
            ]);
        }

        return $country->delete();
    }
}
