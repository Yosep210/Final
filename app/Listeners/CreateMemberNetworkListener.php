<?php

namespace App\Listeners;

use App\Actions\Member\Network\CreateMemberNetworkAction;
use App\Events\MemberRegistered;
use App\Models\Member;
use App\Services\MemberNetworkPlacementService;
use App\Services\MemberRankService;

class CreateMemberNetworkListener
{
    public function __construct(private MemberNetworkPlacementService $placementService, private MemberRankService $rankService) {}

    /**
     * Handle the event.
     */
    public function handle(MemberRegistered $event): void
    {
        $member = $event->member;

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
    }
}
