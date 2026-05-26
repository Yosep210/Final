<?php

namespace App\Actions\Fortify;

use App\Actions\Member\CreateMemberAction;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Data\MemberData;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewMember implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): Member
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        // Convert input array to MemberData and use CreateMemberAction
        // This ensures MemberRegistered event is fired and network is created
        $memberData = new MemberData(
            name: $input['name'],
            username: $input['username'],
            email: $input['email'],
            password: $input['password'],
            status: 'active',
            referralCode: null,
            emailVerifiedAt: null,
            lastLoginAt: null,
        );

        return CreateMemberAction::run($memberData);
    }
}
