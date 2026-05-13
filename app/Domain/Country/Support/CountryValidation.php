<?php

namespace App\Domain\Country\Support;

use App\Models\Country;
use Illuminate\Validation\Rule;

final class CountryValidation
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(?Country $country = null): array
    {
        $ignoreCountry = $country?->id ? Rule::unique('countries', 'iso')->ignore($country) : Rule::unique('countries', 'iso');
        $ignoreIso3 = $country?->id ? Rule::unique('countries', 'iso3')->ignore($country) : Rule::unique('countries', 'iso3');
        $ignoreNumcode = $country?->id ? Rule::unique('countries', 'numcode')->ignore($country) : Rule::unique('countries', 'numcode');
        $ignorePhonecode = $country?->id ? Rule::unique('countries', 'phonecode')->ignore($country) : Rule::unique('countries', 'phonecode');

        return [
            'iso' => ['required', 'string', 'size:2', 'uppercase', $ignoreCountry],
            'name' => ['required', 'string', 'max:255'],
            'nice_name' => ['required', 'string', 'max:255'],
            'iso3' => ['nullable', 'string', 'size:3', 'uppercase', $ignoreIso3],
            'numcode' => ['nullable', 'integer', 'digits_between:1,3', $ignoreNumcode],
            'phonecode' => ['required', 'integer', 'min:1', $ignorePhonecode],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'iso' => 'ISO',
            'name' => 'name',
            'nice_name' => 'nice name',
            'iso3' => 'ISO3',
            'numcode' => 'numeric code',
            'phonecode' => 'phone code',
            'status' => 'status',
        ];
    }
}
