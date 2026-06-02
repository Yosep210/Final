<?php

namespace App\Actions\MemberBank;

use App\Models\MemberBank;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteMemberBankAction
{
    use AsAction;

    public function handle(MemberBank $bank): ?bool
    {
        return $bank->delete();
    }
}
