<?php

namespace App\Http\Requests\District;

use App\Models\District;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDistrictRequest extends FormRequest
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
        /** @var District $district */
        $district = $this->route('district');

        return StoreDistrictRequest::districtRules($this->route('district'));
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return StoreDistrictRequest::attributeLabels();
    }
}
