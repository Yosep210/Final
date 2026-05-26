<?php

namespace App\Console\Commands;

use App\Services\MemberRankService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReEvaluateMemberRanksCommand extends Command
{
    protected $signature = 'rank:re-evaluate';

    protected $description = 'Re-evaluate ranks for all active members (handle demotions)';

    public function handle(MemberRankService $rankService): int
    {
        try {
            $this->info('Re-evaluating member ranks...');

            $results = $rankService->reEvaluateAllRanks();

            $this->line('');
            $this->info('Rank Re-evaluation Results:');
            $this->line('─────────────────────────────────');
            $this->line("✓ Evaluated: {$results['evaluated']} members");
            $this->line("✓ Promoted:  {$results['promoted']} members");
            $this->line("✗ Demoted:   {$results['demoted']} members");
            $this->line("→ Unchanged: {$results['unchanged']} members");
            $this->line('─────────────────────────────────');

            Log::info('Member rank re-evaluation completed', $results);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error re-evaluating ranks: {$e->getMessage()}");

            Log::error('Member rank re-evaluation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
