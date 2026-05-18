<?php

namespace App\Http\Requests\Village;

use App\Models\Village;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVillageRequest extends FormRequest
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
        /** @var Village $village */
        $village = $this->route('village');

        return StoreVillageRequest::villageRules($village);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return StoreVillageRequest::attributeLabels();
    }
}
