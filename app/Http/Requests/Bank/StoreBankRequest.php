<?php

namespace App\Http\Requests\Bank;

use App\Models\Bank;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBankRequest extends FormRequest
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
        return static::bankRules();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return static::attributeLabels();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function bankRules(?Bank $bank = null): array
    {
        $ignoreCode = $bank?->id ? Rule::unique('banks', 'code')->ignore($bank) : Rule::unique('banks', 'code');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', $ignoreCode],
            'type' => ['required', 'string', 'max:50'],
            'flipcode' => ['nullable', 'string', 'max:50'],
            'espaycode' => ['nullable', 'string', 'max:50'],
            'linkitacode' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributeLabels(): array
    {
        return [
            'name' => 'Name',
            'code' => 'Code',
            'type' => 'Type',
            'flipcode' => 'Flip Code',
            'espaycode' => 'Espay Code',
            'linkitacode' => 'Linkita Code',
            'logo' => 'Logo',
        ];
    }
}
