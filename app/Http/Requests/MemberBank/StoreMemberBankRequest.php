<?php

namespace App\Http\Requests\MemberBank;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return static::bankRules();
    }

    public static function bankRules(): array
    {
        return [
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:150'],
        ];
    }

    public static function attributeLabels(): array
    {
        return [
            'bank_name' => 'Bank Name',
            'account_number' => 'Account Number',
            'account_holder' => 'Account Holder Name',
        ];
    }
}
