<?php

namespace App\Http\Requests\Member;

use App\Concerns\PasswordValidationRules;
use App\Domain\Member\Support\MemberValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    use PasswordValidationRules;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...MemberValidation::rules(),
            'password' => $this->passwordRules(),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return MemberValidation::attributes();
    }
}
