<?php

namespace App\Http\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'iso' => ['required', 'string', 'size:2', 'uppercase', Rule::unique('countries', 'iso')],
            'name' => ['required', 'string', 'max:255'],
            'nice_name' => ['required', 'string', 'max:255'],
            'iso3' => ['nullable', 'string', 'size:3', 'uppercase', Rule::unique('countries', 'iso3')],
            'numcode' => ['nullable', 'integer', 'digits_between:1,3', Rule::unique('countries', 'numcode')],
            'phonecode' => ['required', 'integer', 'min:1', Rule::unique('countries', 'phonecode')],
            'status' => ['required', 'boolean'],
        ];
    }
}
