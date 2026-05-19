<?php

namespace App\Domain\Member\Actions;

use App\Domain\Member\Data\MemberData;
use App\Models\Member;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMemberAction
{
    use AsAction;

    public function handle(MemberData $data): Member
    {
        return Member::query()->create($data->toArray());
    }

    public function create(array $input): Member
    {
        return $this->handle(MemberData::fromArray($input));
    }
}
