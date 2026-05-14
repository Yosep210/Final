<?php

namespace App\Domain\Member\Actions;

use App\Domain\Member\Data\MemberData;
use App\Events\MemberCreated;
use App\Models\Member;

class CreateMemberAction
{
    /**
     * Execute the action to create a member.
     */
    public function execute(MemberData $memberData): Member
    {
        $member = Member::query()->create($memberData->toArray());

        MemberCreated::dispatch($member);

        return $member;
    }
}
