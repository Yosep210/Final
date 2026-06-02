<?php

namespace App\Listeners;

use App\Actions\Member\Network\CreateMemberNetworkAction;
use App\Events\MemberRegistered;
use App\Models\Member;
use App\Services\CommissionCalculationService;
use App\Services\MemberNetworkPlacementService;
use App\Services\MemberRankService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateMemberNetworkListener implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public int $backoff = 10; // retry setelah 10 detik

    public function __construct(
        private MemberNetworkPlacementService $placementService,
        private MemberRankService $rankService,
        private CommissionCalculationService $commissionService
    ) {}

    /**
     * Handle the event dengan queue support.
     */
    public function handle(MemberRegistered $event): void
    {
        $member = $event->member;

        try {
            if ($member->network()->exists()) {
                return;
            }

            $placementData = $event->placementData ?? [];
            $sponsorId = null;
            $parentId = null;
            $position = $placementData['position'] ?? null;

            if (! empty($placementData['sponsor_username'])) {
                $sponsorId = Member::where('username', $placementData['sponsor_username'])->value('id');
            }
            if (! empty($placementData['parent_username'])) {
                $parentId = Member::where('username', $placementData['parent_username'])->value('id');
            }

            $networkData = $this->placementService->resolvePlacement(
                $member,
                null,
                $sponsorId,
                $parentId,
                $position,
            );

            $networkModel = CreateMemberNetworkAction::run($networkData);

            // Propagate volume (e.g. default 100 PV) up the ancestor chain
            $regVolume = (float) config('mlm.commission.minimum_volume', 100);
            $this->rankService->propagateVolume($networkModel, $regVolume, $networkData['position'] ?? 'left');

            $sponsor = $networkData['sponsored_id'] ? Member::find($networkData['sponsored_id']) : null;
            $this->placementService->updateSponsorRank($sponsor);

            // Evaluate/assign rank for the new member and optionally for ancestors
            $this->rankService->evaluateAndAssign($member);

            // Calculate Sponsor, Unilevel, and Generation bonuses for the new registration
            if ($sponsor) {
                $this->commissionService->calculateSponsorBonus($member, $sponsor);
            }
            $this->commissionService->calculateUnilevelBonuses($member);
            $this->commissionService->calculateGenerationBonuses($member);

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

            // Re-throw agar retry bisa berjalan
            throw $e;
        }
    }

    /**
     * Handle job failure setelah semua retries habis.
     */
    public function failed(MemberRegistered $event, Throwable $exception): void
    {
        Log::critical('Network creation failed permanently', [
            'member_id' => $event->member->id,
            'member_name' => $event->member->name,
            'error' => $exception->getMessage(),
        ]);

        // TODO: Kirim notifikasi ke admin untuk manual intervention
    }
}
