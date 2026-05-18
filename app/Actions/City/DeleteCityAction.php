<?php

namespace App\Actions\City;

use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteCityAction
{
    use AsAction;

    public function handle(City $city): ?bool
    {
        if (DB::table('districts')->where('city_id', $city->id)->exists()) {
            throw ValidationException::withMessages([
                'city' => 'City cannot be deleted because it is already used by district data.',
            ]);
        }

        return $city->delete();
    }
}
