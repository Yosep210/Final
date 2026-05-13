<?php

namespace App\Http\Requests\Country;

use App\Domain\Country\Support\CountryValidation;
use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;

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

        return CountryValidation::rules($country);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return CountryValidation::attributes();
    }
}
