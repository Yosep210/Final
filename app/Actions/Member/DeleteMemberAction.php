<?php

namespace App\Actions\Member;

use App\Models\Member;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteMemberAction
{
    use AsAction;

    public function handle(Member $member): ?bool
    {
        return $member->delete();
    }
}
