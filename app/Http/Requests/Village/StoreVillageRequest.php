<?php

namespace App\Http\Requests\Village;

use App\Models\Village;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVillageRequest extends FormRequest
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
        return static::villageRules();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return static::attributeLabels();
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function villageRules(?Village $village = null): array
    {
        $ignoreName = $village?->id ? Rule::unique('villages', 'name')->ignore($village) : Rule::unique('villages', 'name');

        return [
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'name' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributeLabels(): array
    {
        return [
            'district_id' => 'District',
            'name' => 'Name',
            'postal_code' => 'Postal code',
        ];
    }
}
