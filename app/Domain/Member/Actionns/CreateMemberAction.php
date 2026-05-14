<?php

namespace App\Domain\Member\Actionns;

use App\Domain\Member\Data\MemberData;
use App\Models\Member;

class CreateMemberAction
{
    /**
     * Execute the action to create a member.
     */
    public function execute(MemberData $memberData): Member
    {
        return Member::query()->create($memberData->toArray());
    }
}
