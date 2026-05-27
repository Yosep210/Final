<?php

namespace App\Http\Requests\Member\Profile;

use App\Models\MemberProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', MemberProfile::class) ?? false; // Authorization logic can be implemented here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public static function rules(?MemberProfile $profile = null): array
    {
        return [
            'member_id' => ['required', 'integer', Rule::exists('members', 'id')],
            'gender' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'string', 'max:255'],
            'country_id' => ['nullable', 'integer'],
            'province_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'district_id' => ['nullable', 'integer'],
            'village_id' => ['nullable', 'integer'],
            'address' => ['nullable', 'string', 'max:255'],
            'id_card_number' => ['nullable', 'string', 'max:255'],
            'id_card_photo' => ['nullable', 'string', 'max:255'],
            'npwp_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return static::attributeLabels();
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function attributeLabels(): array
    {
        return [
            'member_id' => 'Member ID',
            'gender' => 'Gender',
            'birth_date' => 'Birth Date',
            'phone' => 'Phone',
            'profile_photo' => 'Profile Photo',
            'country_id' => 'Country',
            'province_id' => 'Province',
            'city_id' => 'City',
            'district_id' => 'District',
            'village_id' => 'Village',
            'address' => 'Address',
            'id_card_number' => 'ID Card Number',
            'id_card_photo' => 'ID Card Photo',
            'npwp_number' => 'NPWP Number',
        ];
    }
}
