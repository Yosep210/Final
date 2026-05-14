<?php

namespace App\Domain\Member\Support;

use App\Models\Member;
use Illuminate\Validation\Rule;

final class MemberValidation
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(?Member $member = null): array
    {
        $ignoreUsername = $member?->id ? Rule::unique('members', 'username')->ignore($member) : Rule::unique('members', 'username');
        $ignoreEmail = $member?->id ? Rule::unique('members', 'email')->ignore($member) : Rule::unique('members', 'email');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', $ignoreUsername],
            'email' => ['required', 'string', 'email', 'max:255', $ignoreEmail],
            'status' => ['required', 'string', 'in:active,inactive,banned'],
            'referral_code' => ['nullable', 'string', 'max:255'],
            'email_verified_at' => ['nullable', 'date'],
            'last_login_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'name' => 'name',
            'username' => 'username',
            'email' => 'email',
            'status' => 'status',
            'referral_code' => 'referral code',
            'email_verified_at' => 'email verified at',
            'last_login_at' => 'last login at',
        ];
    }
}
