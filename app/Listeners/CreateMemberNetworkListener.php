<?php

namespace App\Listeners;

use App\Actions\Member\Network\CreateMemberNetworkAction;
use App\Events\MemberRegistered;
use App\Models\Member;
use App\Services\MemberNetworkPlacementService;

class CreateMemberNetworkListener
{
    public function __construct(private MemberNetworkPlacementService $placementService) {}

    /**
     * Handle the event.
     */
    public function handle(MemberRegistered $event): void
    {
        $member = $event->member;

        $networkData = $this->placementService->resolvePlacement(
            $member,
            $member->referral_code,
            null,
            null,
            null,
        );

        CreateMemberNetworkAction::run($networkData);

        $sponsor = $networkData['sponsored_id'] ? Member::find($networkData['sponsored_id']) : null;
        $this->placementService->updateSponsorRank($sponsor);
    }
}
