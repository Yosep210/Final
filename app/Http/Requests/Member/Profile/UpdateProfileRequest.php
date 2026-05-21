<?php

namespace App\Http\Requests\Member\Profile;

use App\Models\MemberProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var MemberProfile|null $profile */
        $profile = $this->route('profile');

        return $profile !== null
            && ($this->user()?->can('Update', $profile) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        /** @var MemberProfile $profile */
        $profile = $this->route('profile');

        return [
            ...StoreProfileRequest::rules($profile),
            'member_id' => ['required', 'integer', Rule::exists('members', 'id')],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return StoreProfileRequest::attributeLabels();
    }
}
