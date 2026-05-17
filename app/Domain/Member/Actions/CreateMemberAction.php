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

        return $this->execute(MemberData::fromArray($input));
    }

    public function execute(MemberData $memberData): Member
    {
        $member = Member::query()->create($memberData->toArray());

        MemberCreated::dispatch($member);

        if (method_exists($member, 'assignRole')) {
            $member->assignRole('member');
        }

        return $member->refresh();
    }
}
