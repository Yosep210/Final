<?php

namespace App\Http\Requests\RolePermission;

use App\Models\RolePermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRolePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return static::rolePermissionRules($this->all());
    }

    public function attributes(): array
    {
        return static::attributeLabels();
    }

    public static function rolePermissionRules(array $input = [], ?RolePermission $rolePermission = null): array
    {
        $roleId = $input['role_id'] ?? null;
        $permissionId = $input['permission_id'] ?? null;

        $uniqueRule = Rule::unique('role_has_permissions')
            ->where(fn ($query) => $query->where('permission_id', $permissionId));

        if ($rolePermission !== null) {
            $uniqueRule = $uniqueRule->ignore($rolePermission->role_id, 'role_id');
        }

        return [
            'role_id' => ['required', 'integer', 'exists:roles,id', $uniqueRule],
            'permission_id' => ['required', 'integer', 'exists:permissions,id'],
        ];
    }

    public static function attributeLabels(): array
    {
        return [
            'role_id' => 'Role',
            'permission_id' => 'Permission',
        ];
    }
}
