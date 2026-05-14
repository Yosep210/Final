<?php

namespace App\Domain\Member\Actionns;

use App\Domain\Member\Data\MemberData;
use App\Models\Member;

class UpdateMemberAction
{
    /**
     * Execute the action to update a member.
     */
    public function execute(Member $member, MemberData $memberData): Member
    {
        $member->update($memberData->toArray());

        return $member;
    }
}
