<?php

namespace App\Domain\Country\Rules;

class CreateCountryRules
{
    public function execute(): array
    {
        return [
            'iso' => ['required', 'string', 'max:2', 'unique:countries,iso'],
            'name' => ['required', 'string', 'max:255'],
            'nice_name' => ['required', 'string', 'max:255'], // 'nice_name' harusnya 'niceName' di DTO, tapi di sini mengikuti request
            'iso3' => ['nullable', 'string', 'max:3', 'unique:countries,iso3'], // Bisa null
            'numcode' => ['nullable', 'string', 'max:3', 'unique:countries,numcode'], // Bisa null, max 3 digit
            'phonecode' => ['required', 'integer', 'unique:countries,phonecode'], // Mengubah ke integer
            'status' => ['required', 'boolean'],
        ];
    }
}
