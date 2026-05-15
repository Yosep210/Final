<?php

namespace App\Http\Requests\Province;

use App\Domain\Province\Support\ProvinceValidation;
use App\Models\Province;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProvinceRequest extends FormRequest
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
        /** @var Province $province */
        $province = $this->route('province');

        return ProvinceValidation::rules($province);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ProvinceValidation::attributes();
    }
}
