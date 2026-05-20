<?php

namespace App\Actions\Member;

use App\Data\MemberData;
use App\Models\Member;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMemberAction
{
    use AsAction;

    public function handle(MemberData $data): Member
    {
        return Member::query()->create($data->toArray());
    }
}
