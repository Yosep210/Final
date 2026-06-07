<?php

namespace App\Actions\Member;

use App\Enums\MemberRank;
use App\Events\MemberDemoted;
use App\Events\MemberPromoted;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Role;

class UpdateMemberRankAction
{
    use AsAction;

    public function handle(Member $member, MemberRank $newRank): void
    {
        $network = $member->network;
        if (! $network) {
            return;
        }

        $previousRank = $network->current_rank;
        $newRankValue = $newRank->value;

        if ($newRankValue === $previousRank) {
            return;
        }

        DB::transaction(function () use ($member, $network, $newRankValue, $previousRank) {
            // 1. Update rank in database
            $network->current_rank = $newRankValue;
            $network->save();

            // 2. Sync Spatie Role for the rank (with rank: prefix)
            $roleName = 'rank:'.$newRankValue;
            Role::findOrCreate($roleName, 'web');
            $member->syncRoles([$roleName]);

            // 3. Dispatch promotion/demotion events
            if ($this->isPromotion($previousRank, $newRankValue)) {
                event(new MemberPromoted($member, [
                    'rank' => $newRankValue,
                    'previous_rank' => $previousRank,
                ]));
                Log::info('Member promoted', [
                    'member_id' => $member->id,
                    'from_rank' => $previousRank,
                    'to_rank' => $newRankValue,
                ]);
            } else {
                event(new MemberDemoted($member, [
                    'rank' => $newRankValue,
                    'previous_rank' => $previousRank,
                ]));
                Log::info('Member demoted', [
                    'member_id' => $member->id,
                    'from_rank' => $previousRank,
                    'to_rank' => $newRankValue,
                ]);
            }
        });
    }

    /**
     * Check if transitioning from oldRank to newRank is a promotion.
     */
    private function isPromotion(?string $oldRank, string $newRank): bool
    {
        $hierarchy = [
            'member' => 0,
            'star' => 1,
        ];

        return ($hierarchy[$newRank] ?? -1) > ($hierarchy[$oldRank] ?? -1);
    }
}
