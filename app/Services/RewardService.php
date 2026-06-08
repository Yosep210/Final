<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberNetwork;
use App\Models\MemberRewardPoint;
use App\Models\Reward;
use App\Models\RewardConfig;
use App\Models\RewardPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RewardService
{
    /**
     * Rank hierarchy index mapping.
     */
    protected array $rankHierarchy = [
        'member' => 1,
        'star' => 2,
        'gold' => 3,
        'sapphire' => 4,
        'ruby' => 5,
        'emerald' => 6,
        'diamond' => 7,
        'crown' => 8,
    ];

    /**
     * Calculate left and right reward points for a member.
     */
    public function calculatePoints(Member $member): array
    {
        $network = $member->network;
        if (! $network) {
            return ['left' => 0.0, 'right' => 0.0];
        }

        $pointLeft = $this->calculateLegPoints($member, 'left');
        $pointRight = $this->calculateLegPoints($member, 'right');

        return [
            'left' => $pointLeft,
            'right' => $pointRight,
        ];
    }

    /**
     * Calculate reward points for a specific position (left or right).
     */
    protected function calculateLegPoints(Member $member, string $position): float
    {
        // Find the immediate child on this position
        $child = MemberNetwork::where('parent_id', $member->id)
            ->where('position', $position)
            ->first();

        if (! $child) {
            return 0.0;
        }

        // Find all member IDs in the subtree under this child (inclusive of child)
        $subtreeMemberIds = MemberNetwork::where('path', 'like', $child->path.'/%')
            ->orWhere('member_id', $child->member_id)
            ->pluck('member_id');

        // Sum points from reward_points table where status = 1 (active)
        // and points were created on or after the member's activation date.
        return (float) RewardPoint::whereIn('member_id', $subtreeMemberIds)
            ->where('status', 1)
            ->where('created_at', '>=', $member->created_at)
            ->sum('point');
    }

    /**
     * Process qualifications for all active members.
     */
    public function processQualifications(): int
    {
        $members = Member::where('status', 'active')->get();
        $processedCount = 0;

        foreach ($members as $member) {
            DB::transaction(function () use ($member, &$processedCount) {
                $points = $this->calculatePoints($member);
                $pointLeft = $points['left'];
                $pointRight = $points['right'];
                $pointQualified = min($pointLeft, $pointRight);

                // Update or create member reward points summary
                MemberRewardPoint::updateOrCreate(
                    [
                        'member_id' => $member->id,
                        'type' => 'reward_ro',
                        'period' => 0,
                    ],
                    [
                        'point_left' => $pointLeft,
                        'point_right' => $pointRight,
                        'point_qualified' => $pointQualified,
                        'status' => 1,
                    ]
                );

                // Check and award qualified rewards
                $this->checkAndAwardRewards($member, $pointLeft, $pointRight);
                $processedCount++;
            });
        }

        return $processedCount;
    }

    /**
     * Check and award rewards to a member if they qualify.
     */
    protected function checkAndAwardRewards(Member $member, float $pointLeft, float $pointRight): void
    {
        $pointQualified = min($pointLeft, $pointRight);
        $rewardConfigs = RewardConfig::where('is_active', 1)
            ->orderBy('point', 'asc')
            ->get();

        $currentNetwork = $member->network;
        $currentRankStr = $currentNetwork?->current_rank ?: 'member';
        $currentRankIndex = $this->rankHierarchy[strtolower($currentRankStr)] ?? 0;

        foreach ($rewardConfigs as $config) {
            // Check if already awarded
            $hasReward = Reward::where('member_id', $member->id)
                ->where('reward_config_id', $config->id)
                ->exists();

            if ($hasReward) {
                continue;
            }

            // Expiration validation (only for non-lifetime rewards)
            if (! $config->is_lifetime) {
                $dateLastDay = $member->created_at->copy()->endOfMonth();
                $dateRewardExp = $dateLastDay->addDay()->endOfMonth();

                if (Carbon::now()->greaterThan($dateRewardExp)) {
                    continue;
                }

                // Check overall promo date range if set
                if ($config->start_date && Carbon::now()->lessThan($config->start_date)) {
                    continue;
                }
                if ($config->end_date && Carbon::now()->greaterThan($config->end_date)) {
                    continue;
                }
            }

            // Qualification validation
            if ($pointQualified < $config->point) {
                continue;
            }

            // Award reward
            $adminFee = 0.0;
            $nominalReceipt = $config->nominal - $adminFee;

            Reward::create([
                'member_id' => $member->id,
                'reward_config_id' => $config->id,
                'type' => $config->type ?: 'lifetime',
                'point_qualified' => $config->point,
                'point_left' => $pointLeft,
                'point_right' => $pointRight,
                'rank' => $config->rank,
                'message' => trim($config->message) ?: $config->reward,
                'nominal' => $config->nominal,
                'nominal_receipt' => $nominalReceipt,
                'admin_fund' => $adminFee,
                'tax' => 0.0,
                'bank_name' => $member->bank?->bank_name,
                'bank_code' => $member->bank?->bank_code,
                'account_number' => $member->bank?->account_number,
                'account_holder' => $member->bank?->account_holder,
                'is_trip' => ($config->type === 'trip'),
                'claim' => false,
                'status' => 0, // Pending
            ]);

            // Update member's rank in network if new rank is higher
            if ($config->rank) {
                $rewardRankIndex = $this->rankHierarchy[strtolower($config->rank)] ?? 0;
                if ($rewardRankIndex > $currentRankIndex && $currentNetwork) {
                    $currentNetwork->update(['current_rank' => $config->rank]);
                    $currentRankIndex = $rewardRankIndex;
                }
            }
        }
    }
}
