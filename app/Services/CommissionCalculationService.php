<?php

namespace App\Services;

use App\Models\AutoRoLog;
use App\Models\CommissionLog;
use App\Models\CommissionPayout;
use App\Models\EwalletLog;
use App\Models\Member;
use App\Models\MemberNetwork;
use App\Models\Pin;
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
                ->whereIn('type', ['pairing', 'binary'])
                ->first();

            if ($existing) {
                return $existing;
            }

            return DB::transaction(function () use ($member, $network, $year, $month) {
                // Relock network for update to prevent concurrent updates
                $network = MemberNetwork::where('id', $network->id)->lockForUpdate()->first();

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

                // Apply capping limit (monthly)
                $capLimit = (float) config("mlm.commission.pairing_caps.{$rank}", 1000000);
                $grossCommission = min($grossCommission, $capLimit);

                $taxAmount = $grossCommission * $taxRate;
                $netCommission = $grossCommission - $taxAmount;

                // Create commission log
                $commission = CommissionLog::create([
                    'member_id' => $member->id,
                    'type' => 'pairing',
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
                    'notes' => sprintf('Bonus Pairing (%s BV)', number_format($matchedVolume, 0, ',', '.')),
                    'is_paid' => true,
                    'paid_at' => now(),
                ]);

                // Deduct matched volume from both legs (Carry Forward)
                $network->left_volume = max(0.0, $leftVolume - $matchedVolume);
                $network->right_volume = max(0.0, $rightVolume - $matchedVolume);
                $network->total_volume = max(0.0, (float) ($network->total_volume ?? 0) - (2 * $matchedVolume));
                $network->save();

                $this->logCommissionToEwalletAndAutoRo($commission);

                Log::info('Commission calculated successfully', [
                    'member_id' => $member->id,
                    'commission_id' => $commission->id,
                    'net_commission' => $netCommission,
                ]);

                return $commission;
            });
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
                            $this->createOrUpdatePayout($member, $year, $month);
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

            $payout = CommissionPayout::firstOrNew([
                'member_id' => $member->id,
                'payout_year' => $year,
                'payout_month' => $month,
            ]);

            $amountPaid = (float) ($payout->amount_paid ?? 0);

            $payout->fill([
                'total_amount' => $totalAmount,
                'amount_remaining' => max(0, $totalAmount - $amountPaid),
                'status' => 'pending',
            ]);

            $payout->save();

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

    /**
     * Log commission to eWallet and Auto RO logs based on MLM rules.
     */
    public function logCommissionToEwalletAndAutoRo(CommissionLog $commission): void
    {
        $isPairing = in_array($commission->type, ['pairing', 'binary']);
        $percent = $isPairing ? 80 : 100;

        $netAmount = (float) $commission->net_commission;
        $autoroAmount = $isPairing ? ($netAmount * 0.2) : 0.0;
        $ewalletAmount = $netAmount - $autoroAmount;

        // Create EwalletLog
        EwalletLog::create([
            'member_id' => $commission->member_id,
            'source_id' => $commission->id,
            'source' => 'bonus',
            'category' => 'commission',
            'nominal' => $commission->gross_commission,
            'percent' => $percent,
            'autoro' => $autoroAmount,
            'tax' => $commission->tax_amount,
            'amount' => $ewalletAmount,
            'type' => 'IN',
            'status' => 1,
            'description' => $commission->notes,
        ]);

        // Create AutoRoLog if pairing split
        if ($autoroAmount > 0) {
            AutoRoLog::create([
                'member_id' => $commission->member_id,
                'source_id' => $commission->id,
                'source' => 'bonus',
                'nominal' => $netAmount,
                'percent' => 20,
                'amount' => $autoroAmount,
                'status' => 1,
                'description' => $commission->notes,
            ]);

            $this->checkForAutoRepeatOrder($commission->member_id);
        }
    }

    /**
     * Calculate and log Sponsor bonus for direct sponsor.
     */
    public function calculateSponsorBonus(Member $newMember, Member $sponsor): ?CommissionLog
    {
        try {
            $year = now()->year;
            $month = now()->month;

            $grossCommission = 350000.0;
            $taxRate = 2.5; // 2.5% tax
            $taxAmount = $grossCommission * ($taxRate / 100);
            $netCommission = $grossCommission - $taxAmount;

            $commission = CommissionLog::create([
                'member_id' => $sponsor->id,
                'type' => 'sponsor',
                'source' => (string) $newMember->id,
                'gross_commission' => $grossCommission,
                'tax_amount' => $taxAmount,
                'net_commission' => $netCommission,
                'commission_rate' => 0.0,
                'tax_rate' => $taxRate,
                'member_rank' => $sponsor->network?->current_rank ?? 'member',
                'commission_year' => $year,
                'commission_month' => $month,
                'sponsored_by_id' => $sponsor->id,
                'notes' => "Bonus Sponsor dari pendaftaran member {$newMember->username} (2500 BV)",
                'is_paid' => true,
                'paid_at' => now(),
            ]);

            $this->logCommissionToEwalletAndAutoRo($commission);

            return $commission;
        } catch (\Throwable $e) {
            Log::error('Failed to calculate sponsor bonus', [
                'new_member_id' => $newMember->id,
                'sponsor_id' => $sponsor->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Calculate and log Unilevel bonuses along the placement parent chain (up to 20 levels).
     */
    public function calculateUnilevelBonuses(Member $newMember): void
    {
        if (! config('mlm.unilevel.enabled', false)) {
            return;
        }

        try {
            $current = $newMember->network;
            $level = 1;
            $year = now()->year;
            $month = now()->month;

            while ($current && $current->parent_id && $level <= 20) {
                $parent = Member::find($current->parent_id);
                if (! $parent) {
                    break;
                }

                $grossCommission = 10000.0;
                $taxRate = 2.5;
                $taxAmount = $grossCommission * ($taxRate / 100);
                $netCommission = $grossCommission - $taxAmount;

                $commission = CommissionLog::create([
                    'member_id' => $parent->id,
                    'type' => 'unilevel',
                    'source' => (string) $newMember->id,
                    'gross_commission' => $grossCommission,
                    'tax_amount' => $taxAmount,
                    'net_commission' => $netCommission,
                    'commission_rate' => 0.0,
                    'tax_rate' => $taxRate,
                    'member_rank' => $parent->network?->current_rank ?? 'member',
                    'commission_year' => $year,
                    'commission_month' => $month,
                    'notes' => "Bonus Unilevel Level-{$level} dari pendaftaran member {$newMember->username} (2.500 BV)",
                    'is_paid' => true,
                    'paid_at' => now(),
                ]);

                $this->logCommissionToEwalletAndAutoRo($commission);

                $current = $parent->network;
                $level++;
            }
        } catch (\Throwable $e) {
            Log::error('Failed to calculate unilevel bonuses', [
                'new_member_id' => $newMember->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate and log Generation bonuses along the sponsor referral chain (up to 12 levels).
     */
    public function calculateGenerationBonuses(Member $newMember): void
    {
        if (! config('mlm.generation_bonus.enabled', false)) {
            return;
        }

        try {
            $current = $newMember->network;
            $level = 1;
            $year = now()->year;
            $month = now()->month;

            while ($current && $current->sponsored_id && $level <= 12) {
                $sponsor = Member::find($current->sponsored_id);
                if (! $sponsor) {
                    break;
                }

                $grossCommission = 15000.0;
                $taxRate = 2.5;
                $taxAmount = $grossCommission * ($taxRate / 100);
                $netCommission = $grossCommission - $taxAmount;

                $commission = CommissionLog::create([
                    'member_id' => $sponsor->id,
                    'type' => 'generation',
                    'source' => (string) $newMember->id,
                    'gross_commission' => $grossCommission,
                    'tax_amount' => $taxAmount,
                    'net_commission' => $netCommission,
                    'commission_rate' => 0.0,
                    'tax_rate' => $taxRate,
                    'member_rank' => $sponsor->network?->current_rank ?? 'member',
                    'commission_year' => $year,
                    'commission_month' => $month,
                    'notes' => "Bonus Generation Gen-{$level} dari pendaftaran member {$newMember->username} (2.500 BV)",
                    'is_paid' => true,
                    'paid_at' => now(),
                ]);

                $this->logCommissionToEwalletAndAutoRo($commission);

                $current = $sponsor->network;
                $level++;
            }
        } catch (\Throwable $e) {
            Log::error('Failed to calculate generation bonuses', [
                'new_member_id' => $newMember->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if a member has enough Auto-RO balance to trigger an automatic Repeat Order purchase.
     */
    public function checkForAutoRepeatOrder(int $memberId): void
    {
        DB::transaction(function () use ($memberId) {
            // Lock the member to prevent race conditions on balance checking & deductions
            Member::where('id', $memberId)->lockForUpdate()->first();

            // Get total Auto-RO balance (Sum of amount)
            $balance = (float) AutoRoLog::where('member_id', $memberId)->sum('amount');
            $threshold = (float) config('mlm.auto_ro.package_price_threshold', 1000000.00);

            if ($balance >= $threshold) {
                // Determine how many packages they can buy
                $qty = (int) floor($balance / $threshold);

                for ($i = 0; $i < $qty; $i++) {
                    $deductAmount = -$threshold;

                    // 1. Create a negative AutoRoLog entry for the purchase deduction
                    AutoRoLog::create([
                        'member_id' => $memberId,
                        'source_id' => null,
                        'source' => 'repeat_order',
                        'nominal' => $threshold,
                        'percent' => 100,
                        'amount' => $deductAmount,
                        'status' => 1,
                        'description' => sprintf('Auto Repeat Order Pembelian Paket (Potong Saldo) - Ke-%d', $i + 1),
                    ]);

                    // 2. Generate a new unused PIN for the member representing the RO package product
                    $serialNumber = 'RO-'.strtoupper(str_replace('.', '', uniqid('', true)));
                    $pinCode = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);

                    Pin::create([
                        'serial_number' => $serialNumber,
                        'pin_code' => $pinCode,
                        'status' => 'unused',
                        'owner_id' => $memberId,
                    ]);

                    Log::info('Auto Repeat Order triggered and PIN generated', [
                        'member_id' => $memberId,
                        'amount_deducted' => $threshold,
                        'generated_pin_serial' => $serialNumber,
                    ]);
                }
            }
        });
    }
}
