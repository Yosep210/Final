<?php

namespace App\Domain\Member\Actionns;

use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteMemberAction
{
    /**
     * Execute the action to delete a member.
     */
    public function execute(Member $member): ?bool
    {
        if (DB::table('orders')->where('member_id', $member->id)->exists()) {
            throw ValidationException::withMessages([
                'member' => 'Member cannot be deleted because it is already used by order data.',
            ]);
        }

        return $member->delete();
    }
}
