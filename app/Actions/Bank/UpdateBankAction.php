<?php

namespace App\Actions\Bank;

use App\Data\BankData;
use App\Models\Bank;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateBankAction
{
    use AsAction;

    public function handle(Bank $bank, BankData $data): Bank
    {
        $bank->fill($data->toArray());
        $bank->save();

        return $bank->refresh();
    }
}
