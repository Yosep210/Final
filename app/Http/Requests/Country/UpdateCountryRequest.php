<?php

namespace App\Http\Requests\Country;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends FormRequest
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
        /** @var Country $country */
        $country = $this->route('country');

        return [
            'iso' => ['required', 'string', 'size:2', 'uppercase', Rule::unique('countries', 'iso')->ignore($country)],
            'name' => ['required', 'string', 'max:255'],
            'nice_name' => ['required', 'string', 'max:255'],
            'iso3' => ['nullable', 'string', 'size:3', 'uppercase', Rule::unique('countries', 'iso3')->ignore($country)],
            'numcode' => ['nullable', 'integer', 'digits_between:1,3', Rule::unique('countries', 'numcode')->ignore($country)],
            'phonecode' => ['required', 'integer', 'min:1', Rule::unique('countries', 'phonecode')->ignore($country)],
            'status' => ['required', 'boolean'],
        ];
    }
}
