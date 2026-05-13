<?php

namespace App\Domain\Country\Rules;

use Illuminate\Validation\Rule;

class UpdateCountryRules
{
    /**
     * Mendapatkan aturan validasi untuk pembaruan negara.
     *
     * @param  int  $countryId  ID negara yang sedang diperbarui
     * @return array<string, array<int, mixed>>
     */
    public function execute(int $countryId): array
    {
        return [
            'iso' => ['required', 'string', 'max:2', Rule::unique('countries', 'iso')->ignore($countryId)],
            'name' => ['required', 'string', 'max:255'],
            'nice_name' => ['required', 'string', 'max:255'],
            'iso3' => ['nullable', 'string', 'max:3', Rule::unique('countries', 'iso3')->ignore($countryId)],
            'numcode' => ['nullable', 'string', 'max:3', Rule::unique('countries', 'numcode')->ignore($countryId)],
            'phonecode' => ['required', 'integer', Rule::unique('countries', 'phonecode')->ignore($countryId)],
            'status' => ['required', 'boolean'],
        ];
    }
}
