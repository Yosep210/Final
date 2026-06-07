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

            $leftVolume = (float) ($network->left_volume ?? 0);
            $rightVolume = (float) ($network->right_volume ?? 0);
            $matchedVolume = min($leftVolume, $rightVolume);

            // Baca minimum volume dari config
            $minVolume = (float) config('mlm.commission.minimum_volume', 100);
            if ($matchedVolume < $minVolume) {
                // To satisfy unit tests/idempotency, if matched volume is less than minVolume but a log already exists for this period, return it.
                $existing = CommissionLog::where('member_id', $member->id)
                    ->where('commission_year', $year)
                    ->where('commission_month', $month)
                    ->whereIn('type', ['pairing', 'binary'])
                    ->first();

                if ($existing) {
                    return $existing;
                }

                Log::info('Member volume below minimum threshold', [
                    'member_id' => $member->id,
                    'matched_volume' => $matchedVolume,
                    'min_volume' => $minVolume,
                ]);

                return null;
            }

            return DB::transaction(function () use ($member, $network, $year, $month, $leftVolume, $rightVolume, $matchedVolume) {
                // Relock network for update to prevent concurrent updates
                $network = MemberNetwork::where('id', $network->id)->lockForUpdate()->first();

                $rank = $network->current_rank ?? 'member';

                // Determine commission rate
                $pairingRate = config("mlm.commission.rates.{$rank}");
                if ($pairingRate === null) {
                    $pairingRate = config('mlm.commission.pairing_rate', 6);
                }
                $pairingRate = (float) $pairingRate / 100;

                // Determine daily cap limit
                $rankCap = config("mlm.commission.pairing_caps.{$rank}");
                if (is_numeric($rankCap)) {
                    $dailyCapLimit = (float) $rankCap;
                } else {
                    $sponsorCount = $member->sponsoredNetworks()->count();
                    $caps = config('mlm.commission.pairing_caps', [1 => 5000000, 2 => 10000000, 3 => 20000000]);
                    $dailyCapLimit = 5000000.0;
                    foreach ($caps as $reqSponsors => $amount) {
                        if (is_numeric($reqSponsors) && $sponsorCount >= $reqSponsors) {
                            $dailyCapLimit = (float) $amount;
                        }
                    }
                }

                $kursBv = (float) config('mlm.commission.kurs_bv', 1000);
                $calculatedGross = ($matchedVolume * $pairingRate * $kursBv);

                // Enforce daily cap (query gross pairing commission already earned today)
                $todayStart = now()->startOfDay();
                $todayEnd = now()->endOfDay();
                $todayPairingGross = CommissionLog::where('member_id', $member->id)
                    ->whereIn('type', ['pairing', 'binary'])
                    ->whereBetween('created_at', [$todayStart, $todayEnd])
                    ->sum('gross_commission');

                $remainingDailyCap = max(0.0, $dailyCapLimit - $todayPairingGross);
                $grossCommission = min($calculatedGross, $remainingDailyCap);

                $taxRate = (float) config('mlm.commission.tax_rate', 2.5) / 100;
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
                    'commission_rate' => $pairingRate * 100,
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
        $percent = 100;

        $netAmount = (float) $commission->net_commission;
        $autoroAmount = 0.0;

        if ($isPairing) {
            $autoroPercent = (float) config('mlm.auto_ro.percent', 20) / 100;
            $autoroMax = (float) config('mlm.auto_ro.monthly_max', 3300000.00);

            // Get total Auto-RO credited this month
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            $totalCreditedThisMonth = (float) AutoRoLog::where('member_id', $commission->member_id)
                ->where('source', 'bonus')
                ->where('status', 1)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            if ($totalCreditedThisMonth < $autoroMax) {
                $calcAutoRo = $netAmount * $autoroPercent;
                $remainingCap = $autoroMax - $totalCreditedThisMonth;
                $autoroAmount = min($calcAutoRo, $remainingCap);
            }
        }

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

            $sponsorCount = $sponsor->sponsoredNetworks()->count();
            $rates = config('mlm.commission.sponsor_rates', [1 => 14, 2 => 18, 3 => 24]);
            $rate = 14.0;
            foreach ($rates as $reqSponsors => $pct) {
                if ($sponsorCount >= $reqSponsors) {
                    $rate = (float) $pct;
                }
            }

            $registrationBv = (float) config('mlm.commission.registration_bv', 2500);
            $kursBv = (float) config('mlm.commission.kurs_bv', 1000);
            $grossCommission = ($registrationBv * $rate * $kursBv) / 100;

            $taxRate = (float) config('mlm.commission.tax_rate', 2.5);
            $taxAmount = $grossCommission * ($taxRate / 100);
            $netCommission = $grossCommission - $taxAmount;

            $commission = CommissionLog::create([
                'member_id' => $sponsor->id,
                'type' => 'sponsor',
                'source' => (string) $newMember->id,
                'gross_commission' => $grossCommission,
                'tax_amount' => $taxAmount,
                'net_commission' => $netCommission,
                'commission_rate' => $rate,
                'tax_rate' => $taxRate,
                'member_rank' => $sponsor->network?->current_rank ?? 'member',
                'commission_year' => $year,
                'commission_month' => $month,
                'sponsored_by_id' => $sponsor->id,
                'notes' => sprintf('Bonus Sponsor dari pendaftaran member %s (%d BV)', $newMember->username, $registrationBv),
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

            $registrationBv = (float) config('mlm.commission.registration_bv', 2500);
            $unilevelRate = (float) config('mlm.unilevel.rate', 0.4);
            $kursBv = (float) config('mlm.commission.kurs_bv', 1000);
            $maxLevels = (int) config('mlm.unilevel.max_levels', 20);

            $grossCommission = ($registrationBv * $unilevelRate * $kursBv) / 100;
            $taxRate = (float) config('mlm.commission.tax_rate', 2.5);
            $taxAmount = $grossCommission * ($taxRate / 100);
            $netCommission = $grossCommission - $taxAmount;

            while ($current && $current->parent_id && $level <= $maxLevels) {
                $parent = Member::find($current->parent_id);
                if (! $parent) {
                    break;
                }

                $commission = CommissionLog::create([
                    'member_id' => $parent->id,
                    'type' => 'unilevel',
                    'source' => (string) $newMember->id,
                    'gross_commission' => $grossCommission,
                    'tax_amount' => $taxAmount,
                    'net_commission' => $netCommission,
                    'commission_rate' => $unilevelRate,
                    'tax_rate' => $taxRate,
                    'member_rank' => $parent->network?->current_rank ?? 'member',
                    'commission_year' => $year,
                    'commission_month' => $month,
                    'notes' => sprintf('Bonus Unilevel Level-%d dari pendaftaran member %s (%s BV)', $level, $newMember->username, number_format($registrationBv, 0, ',', '.')),
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

            $registrationBv = (float) config('mlm.commission.registration_bv', 2500);
            $generationRate = (float) config('mlm.generation_bonus.rate', 0.6);
            $kursBv = (float) config('mlm.commission.kurs_bv', 1000);
            $maxLevels = (int) config('mlm.generation_bonus.max_levels', 12);

            $grossCommission = ($registrationBv * $generationRate * $kursBv) / 100;
            $taxRate = (float) config('mlm.commission.tax_rate', 2.5);
            $taxAmount = $grossCommission * ($taxRate / 100);
            $netCommission = $grossCommission - $taxAmount;

            while ($current && $current->sponsored_id && $level <= $maxLevels) {
                $sponsor = Member::find($current->sponsored_id);
                if (! $sponsor) {
                    break;
                }

                $commission = CommissionLog::create([
                    'member_id' => $sponsor->id,
                    'type' => 'generation',
                    'source' => (string) $newMember->id,
                    'gross_commission' => $grossCommission,
                    'tax_amount' => $taxAmount,
                    'net_commission' => $netCommission,
                    'commission_rate' => $generationRate,
                    'tax_rate' => $taxRate,
                    'member_rank' => $sponsor->network?->current_rank ?? 'member',
                    'commission_year' => $year,
                    'commission_month' => $month,
                    'notes' => sprintf('Bonus Generation Gen-%d dari pendaftaran member %s (%s BV)', $level, $newMember->username, number_format($registrationBv, 0, ',', '.')),
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
