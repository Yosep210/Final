<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the member can view any roles.
     */
    public function viewAny(Member $member): bool
    {
        return $member->hasRole('admin') || $member->status === 'active';
    }

    /**
     * Determine whether the member can view the role.
     */
    public function view(Member $member, Role $role): bool
    {
        return $member->hasRole('admin') || $member->status === 'active';
    }

    /**
     * Determine whether the member can create roles.
     */
    public function create(Member $member): bool
    {
        return $member->hasRole('admin');
    }

    /**
     * Determine whether the member can update the role.
     */
    public function update(Member $member, Role $role): bool
    {
        return $member->hasRole('admin');
    }

    /**
     * Determine whether the member can delete the role.
     */
    public function delete(Member $member, Role $role): bool
    {
        return $member->hasRole('admin') && ! $role->users()->exists(); // Business guard: jangan hapus jika ada user yang pakai
    }
}
