<?php

namespace App\Http\Requests\Country;

use App\Domain\Country\Support\CountryValidation;
use Illuminate\Foundation\Http\FormRequest;

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
        return CountryValidation::rules();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return CountryValidation::attributes();
    }
}
