<?php

namespace App\Http\Requests\Member;

use App\Concerns\PasswordValidationRules;
use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...static::memberRules(),
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
        return static::attributeLabels();
    }

    /**
     * @return array<string, mixed>
     */
    public static function memberRules(?Member $member = null): array
    {
        $ignoreUsername = $member?->id
            ? Rule::unique('members', 'username')->ignore($member)
            : Rule::unique('members', 'username');
        $ignoreEmail = $member?->id
            ? Rule::unique('members', 'email')->ignore($member)
            : Rule::unique('members', 'email');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', $ignoreUsername],
            'email' => ['required', 'string', 'email', 'max:255', $ignoreEmail],
            'status' => ['required', 'string', 'max:50'],
            'referral_code' => ['nullable', 'string', 'max:255'],
            'email_verified_at' => ['nullable', 'date'],
            'last_login_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributeLabels(): array
    {
        return [
            'name' => 'name',
            'username' => 'username',
            'email' => 'email',
            'password' => 'password',
            'status' => 'status',
            'referral_code' => 'referral code',
            'email_verified_at' => 'email verified at',
            'last_login_at' => 'last login at',
        ];
    }
}
