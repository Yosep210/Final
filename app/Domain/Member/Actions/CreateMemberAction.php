<?php

namespace App\Domain\Member\Actions;

use App\Concerns\PasswordValidationRules;
use App\Domain\Member\Data\MemberData;
use App\Domain\Member\Support\MemberValidation;
use App\Events\MemberCreated;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateMemberAction implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create the action to create a member.
     */
    public function create(array $input): Member
    {
        $input = array_merge([
            'status' => 'active',
            'referral_code' => null,
            'email_verified_at' => null,
            'last_login_at' => null,
        ], $input);

        Validator::make($input, [
            ...MemberValidation::rules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $memberData = MemberData::fromArray($input);
        $member = Member::query()->create($memberData->toArray());

        MemberCreated::dispatch($member);

        return $member;
    }
}
