<?php

namespace App\Http\Requests\Country;

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

        return StoreCountryRequest::countryRules($country);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return StoreCountryRequest::attributeLabels();
    }
}
