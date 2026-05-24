<?php

namespace App\Concerns;

use App\Models\Member;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate member profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $memberId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'username' => $this->usernameRules($memberId),
            'email' => $this->emailRules($memberId),
            'gender' => ['nullable', 'string', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'integer', Rule::exists('countries', 'id')],
            'province_id' => ['nullable', 'integer', Rule::exists('provinces', 'id')],
            'city_id' => ['nullable', 'integer', Rule::exists('cities', 'id')],
            'district_id' => ['nullable', 'integer', Rule::exists('districts', 'id')],
            'village_id' => ['nullable', 'integer', Rule::exists('villages', 'id')],
            'address' => ['nullable', 'string', 'max:255'],
            'id_card_number' => ['nullable', 'string', 'max:255'],
            'npwp_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validation rules used to validate member names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate member usernames.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function usernameRules(?int $memberId = null): array
    {
        return [
            'required',
            'string',
            'max:255',
            $memberId === null
                ? Rule::unique(Member::class)
                : Rule::unique(Member::class)->ignore($memberId),
        ];
    }

    /**
     * Get the validation rules used to validate member emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $memberId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $memberId === null
                ? Rule::unique(Member::class)
                : Rule::unique(Member::class)->ignore($memberId),
        ];
    }
}
