<?php

namespace App\Domain\Member\Actions;

use App\Events\MemberDeleted;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteMemberAction
{
    /**
     * Execute the action to delete a member.
     */
    public function execute(int $id): ?bool
    {
        $member = Member::query()->find($id);

        if (! $member) {
            throw ValidationException::withMessages([
                'member' => 'Member not found.',
            ]);
        }

        if (DB::table('orders')->where('member_id', $id)->exists()) {
            throw ValidationException::withMessages([
                'member' => 'Member cannot be deleted because it is already used by order data.',
            ]);
        }

        $result = $member->delete();

        MemberDeleted::dispatch($member);

        return $result;
    }
}
