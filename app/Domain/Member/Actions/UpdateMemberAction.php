<?php

namespace App\Domain\Member\Actions;

use App\Domain\Member\Data\MemberData;
use App\Models\Member;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMemberAction
{
    use AsAction;

    public function handle(Member $member, MemberData $data): Member
    {
        $attributes = $data->toArray();

        if ($attributes['password'] === null) {
            unset($attributes['password']);
        }

        $member->fill($attributes);
        $member->save();

        return $member->refresh();
    }
}
