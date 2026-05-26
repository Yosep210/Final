<?php

namespace App\Console\Commands;

use App\Services\CommissionCalculationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateMonthlyCommissionsCommand extends Command
{
    protected $signature = 'commission:calculate-monthly {year? : The year (default: current year)} {month? : The month (default: current month)}';

    protected $description = 'Calculate monthly commissions for all active members';

    public function handle(CommissionCalculationService $commissionService): int
    {
        try {
            $year = $this->argument('year') ? (int) $this->argument('year') : now()->year;
            $month = $this->argument('month') ? (int) $this->argument('month') : now()->month;

            $this->info("Calculating commissions for {$year}-{$month}...");

            $results = $commissionService->calculateMonthlyCommissions($year, $month);

            $this->line('');
            $this->info('Commission Calculation Results:');
            $this->line('─────────────────────────────────');
            $this->line("✓ Success:  {$results['success']} members");
            $this->line("✗ Skipped:  {$results['skipped']} members");
            $this->line("  Total Commission: {$results['total_commission']}");
            $this->line('─────────────────────────────────');

            Log::info('Monthly commission calculation completed', [
                'year' => $year,
                'month' => $month,
                'results' => $results,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error calculating commissions: {$e->getMessage()}");

            Log::error('Monthly commission calculation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
