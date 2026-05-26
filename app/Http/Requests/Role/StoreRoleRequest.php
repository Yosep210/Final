<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StoreRoleRequest extends FormRequest
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
        return static::roleRules();
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
     * @return array<string, mixed>
     */
    public static function roleRules(?Role $role = null): array
    {
        $ignoreName = $role?->id
            ? Rule::unique('roles', 'name')->ignore($role)
            : Rule::unique('roles', 'name');

        return [
            'name' => ['required', 'string', 'max:255', $ignoreName],
            'guard_name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributeLabels(): array
    {
        return [
            'name' => 'name',
            'guard_name' => 'guard name',
        ];
    }
}
