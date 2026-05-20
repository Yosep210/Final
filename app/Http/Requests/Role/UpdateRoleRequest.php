<?php

namespace App\Http\Requests\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Role|int|string|null $role */
        $role = $this->route('role');

        if (! $role instanceof Role && $role !== null) {
            $role = Role::query()->findOrFail($role);
        }

        return StoreRoleRequest::roleRules($role);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return StoreRoleRequest::attributeLabels();
    }
}
