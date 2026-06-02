<?php

namespace App\Actions\MemberBank;

use App\Data\MemberBankData;
use App\Models\MemberBank;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMemberBankAction
{
    use AsAction;

    public function handle(MemberBank $bank, MemberBankData $data): MemberBank
    {
        $bank->fill($data->toArray());
        $bank->save();

        return $bank->refresh();
    }
}
