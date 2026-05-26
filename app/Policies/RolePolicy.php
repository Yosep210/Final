<?php

namespace App\Policies;

use App\Models\Member;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the member can view any roles.
     */
    public function viewAny(Member $member): bool
    {
        return $member->hasRole('Admin') || $member->status === 'active';
    }

    /**
     * Determine whether the member can view the role.
     */
    public function view(Member $member, Role $role): bool
    {
        return $member->hasRole('Admin') || $member->status === 'active';
    }

    /**
     * Determine whether the member can create roles.
     */
    public function create(Member $member): bool
    {
        return $member->hasRole('Admin');
    }

    /**
     * Determine whether the member can update the role.
     */
    public function update(Member $member, Role $role): bool
    {
        return $member->hasRole('Admin');
    }

    /**
     * Determine whether the member can delete the role.
     */
    public function delete(Member $member, Role $role): bool
    {
        return $member->hasRole('Admin') && ! $role->users()->exists(); // Business guard: jangan hapus jika ada user yang pakai
    }
}
