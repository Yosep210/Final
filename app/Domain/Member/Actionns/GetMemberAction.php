<?php

namespace App\Domain\Member\Actionns;

use App\Models\Member;

class GetMemberAction
{
    /**
     * Execute the action to get a member.
     */
    public function execute(int $id): ?Member
    {
        return Member::query()->find($id);
    }
}
