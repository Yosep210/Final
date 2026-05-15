<?php

namespace App\Domain\Role\Support;

use App\Models\Role;
use Illuminate\Validation\Rule;

final class RoleValidation
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(?Role $role = null): array
    {
        $ignoreName = $role?->id ? Rule::unique('roles', 'name')->ignore($role) : Rule::unique('roles', 'name');

        return [
            'name' => ['required', 'string', 'max:255', $ignoreName],
            'guard_name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'name' => 'role name',
            'guard_name' => 'guard name',
        ];
    }
}
