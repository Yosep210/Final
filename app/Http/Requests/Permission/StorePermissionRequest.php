<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class StorePermissionRequest extends FormRequest
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
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return static::permissionRules();
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
     * Get the validation rules for creating or updating a permission.
     *
     * @return array<string, array<mixed>>
     */
    public static function permissionRules(?Permission $permission = null): array
    {
        $ignoreName = $permission?->id ? Rule::unique('permissions', 'name')->ignore($permission) : Rule::unique('permissions', 'name');

        return [
            'name' => ['required', 'string', 'max:255', $ignoreName],
            'guard_name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attribute labels for permissions.
     *
     * @return array<string, string>
     */
    public static function attributeLabels(): array
    {
        return [
            'name' => 'Permission Name',
            'guard_name' => 'Guard Name',
        ];
    }
}
