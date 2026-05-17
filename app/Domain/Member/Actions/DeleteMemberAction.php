<?php

namespace App\Domain\Member\Actions;

use App\Events\MemberDeleted;
use App\Models\Member;

class DeleteMemberAction
{
    public function execute(Member $member): ?bool
    {
        $result = $member->delete();

        MemberDeleted::dispatch($member);

        return $result;
    }
}
