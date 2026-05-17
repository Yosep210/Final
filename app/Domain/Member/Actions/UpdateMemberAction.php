<?php

namespace App\Domain\Member\Actions;

use App\Domain\Member\Data\MemberData;
use App\Events\MemberUpdated;
use App\Models\Member;

class UpdateMemberAction
{
    public function execute(Member $member, MemberData $memberData): Member
    {
        $member->fill($memberData->toArray());
        $member->save();

        MemberUpdated::dispatch($member);

        return $member->refresh();
    }
}
