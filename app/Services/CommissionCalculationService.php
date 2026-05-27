<?php

namespace App\Services;

use App\Models\CommissionLog;
use App\Models\CommissionPayout;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionCalculationService
{
    /**
     * Calculate and log commission for a member for a specific month/year.
     *
     * @param  ?int  $year  Current year if null
     * @param  ?int  $month  Current month if null
     */
    public function calculateBinaryCommission(Member $member, ?int $year = null, ?int $month = null): ?CommissionLog
    {
        try {
            $year = $year ?? now()->year;
            $month = $month ?? now()->month;

            // Get member's network
            $network = $member->network;
            if (! $network) {
                Log::warning('Member has no network for commission calculation', [
                    'member_id' => $member->id,
                ]);

                return null;
            }

            // Check if commission already calculated for this period
            $existing = CommissionLog::where('member_id', $member->id)
                ->where('commission_year', $year)
                ->where('commission_month', $month)
                ->where('type', 'binary')
                ->first();

            if ($existing) {
                return $existing;
            }

            $leftVolume = (float) ($network->left_volume ?? 0);
            $rightVolume = (float) ($network->right_volume ?? 0);
            $matchedVolume = min($leftVolume, $rightVolume);

            // Baca minimum volume dari config
            $minVolume = (float) config('mlm.commission.minimum_volume', 100);
            if ($matchedVolume < $minVolume) {
                Log::info('Member volume below minimum threshold', [
                    'member_id' => $member->id,
                    'matched_volume' => $matchedVolume,
                    'min_volume' => $minVolume,
                ]);

                return null;
            }

            // Baca commission rate dari config berdasarkan rank
            $rank = $network->current_rank ?? 'member';
            $commissionRate = (float) config("mlm.commission.rates.{$rank}", 3) / 100;
            $taxRate = (float) config('mlm.commission.tax_rate', 15) / 100;

            // Calculate commissions
            $grossCommission = $matchedVolume * $commissionRate;
            $taxAmount = $grossCommission * $taxRate;
            $netCommission = $grossCommission - $taxAmount;

            // Create commission log
            $commission = CommissionLog::create([
                'member_id' => $member->id,
                'type' => 'binary',
                'source' => 'network_volume',
                'left_volume' => $leftVolume,
                'right_volume' => $rightVolume,
                'matched_volume' => $matchedVolume,
                'gross_commission' => $grossCommission,
                'tax_amount' => $taxAmount,
                'net_commission' => $netCommission,
                'commission_rate' => $commissionRate * 100,
                'tax_rate' => $taxRate * 100,
                'member_rank' => $rank,
                'commission_year' => $year,
                'commission_month' => $month,
                'sponsored_by_id' => $network->sponsored_id,
                'notes' => sprintf(
                    'Binary commission: %.2f matched volume at %.1f%%',
                    $matchedVolume,
                    $commissionRate * 100
                ),
            ]);

            Log::info('Commission calculated successfully', [
                'member_id' => $member->id,
                'commission_id' => $commission->id,
                'net_commission' => $netCommission,
            ]);

            return $commission;
        } catch (\Throwable $e) {
            Log::error('Failed to calculate commission', [
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Calculate commissions for all active members for a specific month.
     */
    public function calculateMonthlyCommissions(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return DB::transaction(function () use ($year, $month) {
            $results = [
                'success' => 0,
                'failed' => 0,
                'skipped' => 0,
                'total_commission' => 0,
            ];

            Member::query()
                ->where('status', 'active')
                ->with('network')
                ->chunk(100, function ($members) use (&$results, $year, $month) {
                    foreach ($members as $member) {
                        $commission = $this->calculateBinaryCommission($member, $year, $month);

                        if ($commission) {
                            $results['success']++;
                            $results['total_commission'] += $commission->net_commission;
                        } else {
                            $results['skipped']++;
                        }
                    }
                });

            return $results;
        });
    }

    /**
     * Create or update payout for a member for a specific month.
     */
    public function createOrUpdatePayout(Member $member, ?int $year = null, ?int $month = null): ?CommissionPayout
    {
        try {
            $year = $year ?? now()->year;
            $month = $month ?? now()->month;

            // Get all commission logs for this period
            $totalAmount = (float) CommissionLog::where('member_id', $member->id)
                ->where('commission_year', $year)
                ->where('commission_month', $month)
                ->sum('net_commission');

            if ($totalAmount <= 0) {
                return null;
            }

            $payout = CommissionPayout::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'payout_year' => $year,
                    'payout_month' => $month,
                ],
                [
                    'total_amount' => $totalAmount,
                    'amount_remaining' => DB::raw("GREATEST(0, {$totalAmount} - COALESCE(amount_paid, 0))"),
                    'status' => 'pending',
                ]
            );

            return $payout;
        } catch (\Throwable $e) {
            Log::error('Failed to create/update payout', [
                'member_id' => $member->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get commission summary for a member for a period.
     */
    public function getCommissionSummary(Member $member, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $commissions = CommissionLog::where('member_id', $member->id)
            ->where('commission_year', $year)
            ->where('commission_month', $month)
            ->get();

        return [
            'member_id' => $member->id,
            'period' => sprintf('%04d-%02d', $year, $month),
            'total_gross' => $commissions->sum('gross_commission'),
            'total_tax' => $commissions->sum('tax_amount'),
            'total_net' => $commissions->sum('net_commission'),
            'commission_count' => $commissions->count(),
            'commissions' => $commissions->toArray(),
        ];
    }
}
