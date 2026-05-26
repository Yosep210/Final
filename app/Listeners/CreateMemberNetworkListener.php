<?php

namespace App\Listeners;

use App\Actions\Member\Network\CreateMemberNetworkAction;
use App\Events\MemberRegistered;
use App\Models\Member;
use App\Services\MemberNetworkPlacementService;
use App\Services\MemberRankService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateMemberNetworkListener
{
    public function __construct(private MemberNetworkPlacementService $placementService, private MemberRankService $rankService) {}

    /**
     * Handle the event with comprehensive error handling.
     */
    public function handle(MemberRegistered $event): void
    {
        $member = $event->member;

        try {
            if ($member->network()->exists()) {
                return;
            }

            $networkData = $this->placementService->resolvePlacement(
                $member,
                null,
                null,
                null,
                null,
            );

            CreateMemberNetworkAction::run($networkData);

            $sponsor = $networkData['sponsored_id'] ? Member::find($networkData['sponsored_id']) : null;
            $this->placementService->updateSponsorRank($sponsor);

            // Evaluate/assign rank for the new member and optionally for ancestors
            $this->rankService->evaluateAndAssign($member);

            Log::info('Member network created successfully', [
                'member_id' => $member->id,
                'member_name' => $member->name,
                'parent_id' => $networkData['parent_id'] ?? null,
                'sponsor_id' => $networkData['sponsored_id'] ?? null,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to create member network', [
                'member_id' => $member->id,
                'member_name' => $member->name ?? 'Unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Don't re-throw - allow member registration to complete
            // Network can be manually created or re-attempted later
            // In production, you might want to notify admins via alert/queue
        }
    }
}
