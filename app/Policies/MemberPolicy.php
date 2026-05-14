<?php

namespace App\Policies;

use App\Models\Member;

class MemberPolicy
{
    /**
     * Determine whether the member can view any members.
     */
    public function viewAny(Member $member): bool
    {
        return $member->status === 'active';
    }

    /**
     * Determine whether the member can view the member.
     */
    public function view(Member $member, Member $model): bool
    {
        return $member->id === $model->id || $member->status === 'active';
    }

    /**
     * Determine whether the member can create members.
     */
    public function create(Member $member): bool
    {
        return $member->status === 'active';
    }

    /**
     * Determine whether the member can update the member.
     */
    public function update(Member $member, Member $model): bool
    {
        return $member->id === $model->id || $member->status === 'active';
    }

    /**
     * Determine whether the member can delete the member.
     */
    public function delete(Member $member, Member $model): bool
    {
        return $member->id === $model->id || $member->status === 'active';
    }

    /**
     * Determine whether the member can restore the member.
     */
    public function restore(Member $member, Member $model): bool
    {
        return $member->status === 'active';
    }

    /**
     * Determine whether the member can permanently delete the member.
     */
    public function forceDelete(Member $member, Member $model): bool
    {
        return $member->status === 'active';
    }
}
