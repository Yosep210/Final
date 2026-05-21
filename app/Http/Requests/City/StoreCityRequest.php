<?php

namespace App\Http\Requests\City;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
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
        return static::cityRules();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return static::attributeLabels();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function cityRules(?City $city = null): array
    {
        return [
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributeLabels(): array
    {
        return [
            'province_id' => 'Province',
            'name' => 'name',
        ];
    }
}
