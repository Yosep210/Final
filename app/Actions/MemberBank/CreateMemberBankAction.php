<?php

namespace App\Actions\MemberBank;

use App\Data\MemberBankData;
use App\Models\MemberBank;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMemberBankAction
{
    use AsAction;

    public function handle(MemberBankData $data): MemberBank
    {
        return MemberBank::query()->create($data->toArray());
    }
}
