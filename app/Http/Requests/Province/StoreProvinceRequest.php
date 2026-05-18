<?php

namespace App\Http\Requests\Province;

use App\Models\Province;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProvinceRequest extends FormRequest
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
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return static::provinceRules();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return static::attributeLabels();
    }

    /**
     * Get the validation rules for creating or updating a province.
     *
     * @return array<string, array<mixed>>
     */
    public static function provinceRules(?Province $province = null): array
    {
        $ignoreName = $province?->id ? Rule::unique('provincies', 'name')->ignore($province) : Rule::unique('provincies', 'name');

        return [
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255', $ignoreName],
            'code' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom attribute labels for provinces.
     *
     * @return array<string, string>
     */
    public static function attributeLabels(): array
    {
        return [
            'country_id' => 'country',
            'name' => 'name',
            'code' => 'code',
        ];
    }
}
