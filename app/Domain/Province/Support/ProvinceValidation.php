<?php

namespace App\Domain\Province\Support;

class ProvinceValidation
{
    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'country_id' => 'country',
            'name' => 'name',
        ];
    }
}
