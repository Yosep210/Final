<?php

namespace App\Actions\MemberBank;

use App\Models\MemberBank;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetMemberBankAction
{
    use AsAction;

    public function handle(): Collection
    {
        return MemberBank::all();
    }
}
