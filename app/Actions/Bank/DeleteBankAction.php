<?php

namespace App\Actions\Bank;

use App\Models\Bank;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteBankAction
{
    use AsAction;

    public function handle(Bank $bank): ?bool
    {
        return $bank->delete();
    }
}
