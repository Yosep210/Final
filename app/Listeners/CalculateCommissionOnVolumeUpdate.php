<?php

namespace App\Listeners;

use App\Events\MemberVolumeUpdated;
use App\Services\CommissionCalculationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class CalculateCommissionOnVolumeUpdate implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public int $backoff = 10; // retry setelah 10 detik

    public function __construct(private CommissionCalculationService $commissionService) {}

    /**
     * Handle the event dengan queue support.
     */
    public function handle(MemberVolumeUpdated $event): void
    {
        try {
            $member = $event->member;

            Log::info('Calculating commission after volume update', [
                'member_id' => $member->id,
                'previous_volume' => $event->previousVolume,
                'current_volume' => $event->currentVolume,
            ]);

            // Calculate commission for current month
            $commission = $this->commissionService->calculateBinaryCommission($member);

            if ($commission) {
                // Create or update payout
                $payout = $this->commissionService->createOrUpdatePayout($member);

                Log::info('Commission calculated and payout created', [
                    'member_id' => $member->id,
                    'commission_id' => $commission->id,
                    'payout_id' => $payout?->id,
                    'net_commission' => $commission->net_commission,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Failed to calculate commission on volume update', [
                'member_id' => $event->member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw untuk memungkinkan retry
            throw $e;
        }
    }

    /**
     * Handle job failure setelah semua retries habis.
     */
    public function failed(MemberVolumeUpdated $event, Throwable $exception): void
    {
        Log::critical('Commission calculation failed permanently', [
            'member_id' => $event->member->id,
            'error' => $exception->getMessage(),
        ]);

        // TODO: Notifikasi admin untuk manual check
    }
}
