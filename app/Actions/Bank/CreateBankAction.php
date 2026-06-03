<?php

namespace App\Actions\Bank;

use App\Data\BankData;
use App\Models\Bank;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateBankAction
{
    use AsAction;

    public function handle(BankData $data): Bank
    {
        return Bank::query()->create($data->toArray());
    }
}
