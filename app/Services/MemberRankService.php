<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberNetwork;
use Illuminate\Support\Facades\DB;

class MemberRankService
{
    /**
     * Evaluate and assign rank for a member based on metrics.
     * This is a basic example — adapt thresholds to your business rules.
     */
    public function evaluateAndAssign(Member $member): ?string
    {
        $network = MemberNetwork::where('member_id', $member->id)->first();

        if (! $network) {
            return null;
        }

        $left = (float) $network->left_volume;
        $right = (float) $network->right_volume;
        $personal = Member::where('sponsored_id', $member->id)->count();

        // Example thresholds
        if ($personal >= 4 && $left >= 2000 && $right >= 2000) {
            $rank = 'gold';
        } elseif ($personal >= 2 && $left >= 500 && $right >= 500) {
            $rank = 'silver';
        } elseif ($personal >= 1) {
            $rank = 'bronze';
        } else {
            $rank = 'member';
        }

        // persist atomically
        DB::transaction(function () use ($network, $rank) {
            $network->current_rank = $rank;
            $network->save();
        });

        return $rank;
    }

    /**
     * Quick helper to update volumes up the ancestor chain.
     */
    public function propagateVolume(MemberNetwork $network, float $amount, string $side): void
    {
        $current = $network;

        while ($current && $current->parent_id) {
            $parent = MemberNetwork::where('member_id', $current->parent_id)->first();

            if (! $parent) {
                break;
            }

            if ($current->position === 'left') {
                $parent->left_volume = ($parent->left_volume ?? 0) + $amount;
            } else {
                $parent->right_volume = ($parent->right_volume ?? 0) + $amount;
            }

            $parent->total_volume = ($parent->total_volume ?? 0) + $amount;
            $parent->save();

            $current = $parent;
        }
    }
}
