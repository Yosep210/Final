<?php

namespace App\Console\Commands;

use App\Services\RewardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessRewardQualification extends Command
{
    protected $signature = 'reward:process-qualification';

    protected $description = 'Calculate left/right subtree reward points for members, update summaries, and award qualified rewards';

    public function handle(RewardService $rewardService): int
    {
        try {
            $this->info('Starting member reward qualification processing...');

            $count = $rewardService->processQualifications();

            $this->info("Successfully processed reward qualifications for {$count} members.");

            Log::info('Reward qualification processing completed', [
                'processed_members_count' => $count,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error processing reward qualifications: {$e->getMessage()}");

            Log::error('Reward qualification processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
