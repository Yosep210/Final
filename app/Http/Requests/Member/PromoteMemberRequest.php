<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class PromoteMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('member')) ?? false;
    }

    public function rules(): array
    {
        return [
            'rank' => ['required', 'string', 'max:64'],
            'generation' => ['nullable', 'integer', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function validatedPayload(): array
    {
        return $this->validated();
    }
}
