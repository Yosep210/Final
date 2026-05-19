<?php

namespace App\Http\Requests\RolePermission;

use App\Models\RolePermission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRolePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $existing = RolePermission::query()
            ->where('role_id', $this->route('role_id'))
            ->where('permission_id', $this->route('permission_id'))
            ->first();

        return StoreRolePermissionRequest::rolePermissionRules($this->all(), $existing);
    }

    public function attributes(): array
    {
        return StoreRolePermissionRequest::attributeLabels();
    }
}
