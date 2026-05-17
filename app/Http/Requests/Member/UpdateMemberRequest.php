<?php

namespace App\Http\Requests\Member;

use App\Domain\Member\Support\MemberValidation;
use App\Models\Member;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Member $member */
        $member = $this->route('member');

        return [
            ...MemberValidation::rules($member),
            'password' => ['nullable', 'string', Password::default(), 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return MemberValidation::attributes();
    }
}
