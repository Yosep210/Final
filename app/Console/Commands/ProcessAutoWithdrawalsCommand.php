<?php

namespace App\Console\Commands;

use App\Services\WithdrawalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessAutoWithdrawalsCommand extends Command
{
    protected $signature = 'withdraw:process-auto';

    protected $description = 'Process automated withdrawals for eligible members based on their auto withdrawal settings';

    public function handle(WithdrawalService $withdrawalService): int
    {
        try {
            $this->info('Starting auto-withdrawal processing...');

            $count = $withdrawalService->processAutoWithdrawals();

            $this->info("Successfully processed {$count} auto-withdrawals.");

            Log::info('Auto-withdrawal processing completed', [
                'processed_count' => $count,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error processing auto-withdrawals: {$e->getMessage()}");

            Log::error('Auto-withdrawal processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
