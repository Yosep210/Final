<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return StoreCityRequest::cityRules();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return StoreCityRequest::attributeLabels();
    }
}
