<?php

namespace App\Http\Requests\Bank;

use App\Models\Bank;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Bank $bank */
        $bank = $this->route('bank');

        return StoreBankRequest::bankRules($bank);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return StoreBankRequest::attributeLabels();
    }
}
