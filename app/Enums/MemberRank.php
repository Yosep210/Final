<?php

namespace App\Enums;

enum MemberRank: string
{
    case MEMBER = 'member';
    case STAR = 'star';

    /**
     * Get the human-readable label for the rank.
     */
    public function label(): string
    {
        return match ($this) {
            self::MEMBER => 'Member',
            self::STAR => 'Star',
        };
    }
}
