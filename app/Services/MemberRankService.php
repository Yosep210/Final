<?php

namespace App\Services;

use App\Events\MemberDemoted;
use App\Events\MemberPromoted;
use App\Events\MemberVolumeUpdated;
use App\Models\Member;
use App\Models\MemberNetwork;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberRankService
{
    /**
     * Evaluate and assign rank for a member based on config rules.
     * Uses thresholds defined in config/mlm.php
     */
    public function evaluateAndAssign(Member $member): ?string
    {
        $network = MemberNetwork::where('member_id', $member->id)->first();

        if (! $network) {
            return null;
        }

        $rank = $this->determineRank($member, $network);
        $previousRank = $network->current_rank;

        // Only update if rank changed
        if ($rank === $previousRank) {
            return $rank;
        }

        // Persist atomically
        DB::transaction(function () use ($network, $rank, $previousRank) {
            $network->current_rank = $rank;
            $network->save();

            // Trigger promotion or demotion event
            if ($this->isPromotion($previousRank, $rank)) {
                event(new MemberPromoted($network->member, ['rank' => $rank, 'previous_rank' => $previousRank]));
                Log::info('Member promoted', [
                    'member_id' => $network->member_id,
                    'from_rank' => $previousRank,
                    'to_rank' => $rank,
                ]);
            } else {
                event(new MemberDemoted($network->member, ['rank' => $rank, 'previous_rank' => $previousRank]));
                Log::info('Member demoted', [
                    'member_id' => $network->member_id,
                    'from_rank' => $previousRank,
                    'to_rank' => $rank,
                ]);
            }
        });

        return $rank;
    }

    /**
     * Determine the appropriate rank based on member metrics and config.
     */
    private function determineRank(Member $member, MemberNetwork $network): string
    {
        $left = (float) ($network->left_volume ?? 0);
        $right = (float) ($network->right_volume ?? 0);
        $personal = MemberNetwork::where('sponsored_id', $member->id)->count();

        $ranks = config('mlm.ranks', []);

        // Check from highest to lowest rank
        $rankHierarchy = ['gold', 'silver', 'bronze', 'member'];

        foreach ($rankHierarchy as $rankName) {
            if (! isset($ranks[$rankName])) {
                continue;
            }

            $requirements = $ranks[$rankName];

            if ($this->meetsRequirements($personal, $left, $right, $requirements)) {
                return $rankName;
            }
        }

        return 'member';
    }

    /**
     * Check if member meets requirements for a rank.
     */
    private function meetsRequirements(int $personal, float $left, float $right, array $requirements): bool
    {
        return $personal >= ($requirements['personal_recruits'] ?? 0)
            && $left >= ($requirements['left_volume'] ?? 0)
            && $right >= ($requirements['right_volume'] ?? 0);
    }

    /**
     * Check if transitioning from oldRank to newRank is a promotion.
     */
    private function isPromotion(?string $oldRank, string $newRank): bool
    {
        $hierarchy = ['member' => 0, 'bronze' => 1, 'silver' => 2, 'gold' => 3];

        return ($hierarchy[$newRank] ?? -1) > ($hierarchy[$oldRank] ?? -1);
    }

    /**
     * Re-evaluate all members' ranks (for scheduled tasks).
     * Useful for demotions when members don't maintain volume.
     */
    public function reEvaluateAllRanks(): array
    {
        $results = [
            'evaluated' => 0,
            'promoted' => 0,
            'demoted' => 0,
            'unchanged' => 0,
        ];

        Member::query()
            ->where('status', 'active')
            ->with('network')
            ->chunk(100, function ($members) use (&$results) {
                foreach ($members as $member) {
                    $previousRank = $member->network?->current_rank;
                    $newRank = $this->evaluateAndAssign($member);

                    $results['evaluated']++;

                    if ($newRank === $previousRank) {
                        $results['unchanged']++;
                    } elseif ($this->isPromotion($previousRank, $newRank)) {
                        $results['promoted']++;
                    } else {
                        $results['demoted']++;
                    }
                }
            });

        return $results;
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

            $previousVolume = [
                'left' => (float) ($parent->left_volume ?? 0),
                'right' => (float) ($parent->right_volume ?? 0),
                'total' => (float) ($parent->total_volume ?? 0),
            ];

            if ($current->position === 'left') {
                $parent->left_volume = ($parent->left_volume ?? 0) + $amount;
            } else {
                $parent->right_volume = ($parent->right_volume ?? 0) + $amount;
            }

            $parent->total_volume = ($parent->total_volume ?? 0) + $amount;
            $parent->save();

            $currentVolume = [
                'left' => (float) $parent->left_volume,
                'right' => (float) $parent->right_volume,
                'total' => (float) $parent->total_volume,
            ];

            // Trigger commission calculation by dispatching the volume updated event
            if ($parent->member) {
                event(new MemberVolumeUpdated($parent->member, $previousVolume, $currentVolume));
            }

            $current = $parent;
        }
    }
}
