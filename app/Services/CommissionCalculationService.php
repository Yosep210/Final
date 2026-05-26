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
     * Commission rate configuration (can be moved to config/mlm.php)
     */
    private const BINARY_COMMISSION_RATES = [
        'member' => 0.03,      // 3%
        'bronze' => 0.05,      // 5%
        'silver' => 0.07,      // 7%
        'gold' => 0.10,        // 10%
    ];

    private const TAX_RATE = 0.15; // 15% tax

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

            $leftVolume = $network->left_volume ?? 0;
            $rightVolume = $network->right_volume ?? 0;
            $matchedVolume = min($leftVolume, $rightVolume);

            // Minimum volume threshold (can be configured)
            $minVolume = 100;
            if ($matchedVolume < $minVolume) {
                Log::info('Member volume below minimum threshold', [
                    'member_id' => $member->id,
                    'matched_volume' => $matchedVolume,
                    'min_volume' => $minVolume,
                ]);

                return null;
            }

            // Get commission rate based on rank
            $rank = $network->current_rank ?? 'member';
            $commissionRate = self::BINARY_COMMISSION_RATES[$rank] ?? self::BINARY_COMMISSION_RATES['member'];

            // Calculate commissions
            $grossCommission = $matchedVolume * $commissionRate;
            $taxAmount = $grossCommission * self::TAX_RATE;
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
                'commission_rate' => $commissionRate * 100, // Store as percentage
                'tax_rate' => self::TAX_RATE * 100,
                'member_rank' => $rank,
                'commission_year' => $year,
                'commission_month' => $month,
                'sponsored_by_id' => $network->sponsored_id,
                'notes' => "Binary commission: {$matchedVolume} matched volume at ".($commissionRate * 100).'%',
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
            $members = Member::query()
                ->where('status', 'active')
                ->with('network')
                ->get();

            $results = [
                'success' => 0,
                'failed' => 0,
                'skipped' => 0,
                'total_commission' => 0,
            ];

            foreach ($members as $member) {
                $commission = $this->calculateBinaryCommission($member, $year, $month);

                if ($commission) {
                    $results['success']++;
                    $results['total_commission'] += $commission->net_commission;
                } else {
                    $results['skipped']++;
                }
            }

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
            $commissions = CommissionLog::where('member_id', $member->id)
                ->where('commission_year', $year)
                ->where('commission_month', $month)
                ->get();

            if ($commissions->isEmpty()) {
                return null;
            }

            $totalAmount = $commissions->sum('net_commission');

            // Find existing payout
            $payout = CommissionPayout::firstOrCreate(
                [
                    'member_id' => $member->id,
                    'payout_year' => $year,
                    'payout_month' => $month,
                ],
                [
                    'total_amount' => $totalAmount,
                    'amount_remaining' => $totalAmount,
                    'status' => 'pending',
                ]
            );

            // Update amounts if commissions changed
            if ($payout->total_amount !== $totalAmount) {
                $payout->update([
                    'total_amount' => $totalAmount,
                    'amount_remaining' => $totalAmount - $payout->amount_paid,
                ]);
            }

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
