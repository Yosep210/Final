<?php

namespace App\Domain\Member\Actions;

use App\Domain\Member\Data\MemberData;
use App\Events\MemberUpdated;
use App\Models\Member;
use Illuminate\Validation\ValidationException;

class UpdateMemberAction
{
    /**
     * Execute the action to update a member.
     */
    public function execute(int $id, MemberData $memberData): Member
    {
        $member = Member::query()->find($id);

        if (! $member) {
            throw ValidationException::withMessages([
                'member' => 'Member not found.',
            ]);
        }

        $member->update($memberData->toArray());

        MemberUpdated::dispatch($member);

        return $member;
    }
}
