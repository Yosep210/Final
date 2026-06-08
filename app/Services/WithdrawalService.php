<?php

namespace App\Services;

use App\Models\EwalletLog;
use App\Models\Member;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WithdrawalService
{
    /**
     * Request a manual withdrawal for a member.
     *
     * @throws \Exception
     */
    public function requestManualWithdrawal(Member $member, float $nominal, string $password): Withdrawal
    {
        // 1. Check if member is admin (Admins cannot withdraw)
        if ($member->hasRole('Admin')) {
            throw new \Exception(__('Administrator tidak diperbolehkan melakukan penarikan dana.'));
        }

        // 2. Check member status
        if ($member->status !== 'active') {
            throw new \Exception(__('Akun Anda tidak aktif.'));
        }

        // 3. Check wd_status (1 = Manual Withdraw enabled)
        if ($member->wd_status !== 1) {
            throw new \Exception(__('Fitur penarikan manual akun Anda dinonaktifkan.'));
        }

        // 4. Validate bank details
        $bank = $member->bank;
        if (! $bank || empty($bank->bank_name) || empty($bank->account_number) || empty($bank->account_holder)) {
            throw new \Exception(__('Data bank belum diisi lengkap. Silakan lengkapi data bank di halaman profil Anda.'));
        }

        // 5. Verify password
        if (! Hash::check($password, $member->password)) {
            throw new \Exception(__('Password transaksi salah.'));
        }

        // 6. Validate nominal limits
        $minWd = (float) config('mlm.payout.minimum_commission', 50000);
        if ($nominal < $minWd) {
            throw new \Exception(__('Nominal penarikan minimal Rp '.number_format($minWd, 0, ',', '.')));
        }

        // 7. Verify balance
        $balance = $member->ewalletBalance();
        if ($nominal > $balance) {
            throw new \Exception(__('Saldo eWallet tidak mencukupi. Saldo Anda saat ini: Rp '.number_format($balance, 0, ',', '.')));
        }

        return DB::transaction(function () use ($member, $bank, $nominal) {
            $taxRate = 0.0; // Flat 0% tax on withdrawal
            $tax = $nominal * ($taxRate / 100);
            $adminFee = (float) config('mlm.payout.fee', 5000);
            $amountReceipt = $nominal - $tax - $adminFee;

            if ($amountReceipt <= 0) {
                throw new \Exception(__('Nominal penarikan terlalu kecil setelah dipotong biaya transfer.'));
            }

            // Create withdrawal log (status 0 = pending)
            $withdrawal = Withdrawal::create([
                'member_id' => $member->id,
                'type' => 'ewallet',
                'bank_name' => $bank->bank_name,
                'account_number' => $bank->account_number,
                'account_holder' => $bank->account_holder,
                'nominal' => $nominal,
                'nominal_receipt' => $amountReceipt,
                'tax' => $tax,
                'auto_ro' => 0.0,
                'admin_fund' => $adminFee,
                'status' => 0,
            ]);

            // Save eWallet OUT log
            EwalletLog::create([
                'member_id' => $member->id,
                'source_id' => $withdrawal->id,
                'source' => 'withdraw',
                'category' => 'commission',
                'nominal' => $nominal,
                'percent' => 100.0,
                'autoro' => 0.0,
                'tax' => $tax,
                'amount' => $nominal,
                'type' => 'OUT',
                'status' => 1,
                'description' => sprintf('Withdraw tgl %s Rp %s', now()->format('Y-m-d'), number_format($nominal, 0, ',', '.')),
            ]);

            Log::info('Manual withdrawal requested successfully', [
                'member_id' => $member->id,
                'withdrawal_id' => $withdrawal->id,
                'nominal' => $nominal,
            ]);

            return $withdrawal;
        });
    }

    /**
     * Process auto-withdrawals for all eligible members.
     *
     * @return int Number of processed auto-withdrawals
     */
    public function processAutoWithdrawals(): int
    {
        $processedCount = 0;

        // Find all members with active status and wd_status = 2 (Auto Withdraw)
        $members = Member::where('status', 'active')
            ->where('wd_status', 2)
            ->whereHas('bank')
            ->get();

        foreach ($members as $member) {
            try {
                DB::transaction(function () use ($member, &$processedCount) {
                    $bank = $member->bank;
                    if (! $bank || empty($bank->bank_name) || empty($bank->account_number) || empty($bank->account_holder)) {
                        return;
                    }

                    $balance = $member->ewalletBalance();

                    // Minimum withdraw setting for auto-withdraw
                    $minWd = $member->wd_min > 0 ? (float) $member->wd_min : 100000.0;
                    $absoluteMin = (float) config('mlm.payout.minimum_commission', 50000);
                    $minWd = max($minWd, $absoluteMin);

                    if ($balance < $minWd) {
                        return;
                    }

                    // Auto withdraw all available balance
                    $nominal = $balance;

                    $taxRate = 0.0;
                    $tax = $nominal * ($taxRate / 100);
                    $adminFee = (float) config('mlm.payout.fee', 5000);
                    $amountReceipt = $nominal - $tax - $adminFee;

                    if ($amountReceipt <= 0) {
                        return;
                    }

                    // Create withdrawal log (status 0 = pending)
                    $withdrawal = Withdrawal::create([
                        'member_id' => $member->id,
                        'type' => 'ewallet',
                        'bank_name' => $bank->bank_name,
                        'account_number' => $bank->account_number,
                        'account_holder' => $bank->account_holder,
                        'nominal' => $nominal,
                        'nominal_receipt' => $amountReceipt,
                        'tax' => $tax,
                        'auto_ro' => 0.0,
                        'admin_fund' => $adminFee,
                        'status' => 0,
                    ]);

                    // Save eWallet OUT log
                    EwalletLog::create([
                        'member_id' => $member->id,
                        'source_id' => $withdrawal->id,
                        'source' => 'withdraw',
                        'category' => 'commission',
                        'nominal' => $nominal,
                        'percent' => 100.0,
                        'autoro' => 0.0,
                        'tax' => $tax,
                        'amount' => $nominal,
                        'type' => 'OUT',
                        'status' => 1,
                        'description' => sprintf('Withdraw Otomatis tgl %s Rp %s', now()->format('Y-m-d'), number_format($nominal, 0, ',', '.')),
                    ]);

                    Log::info('Auto withdrawal processed successfully', [
                        'member_id' => $member->id,
                        'withdrawal_id' => $withdrawal->id,
                        'nominal' => $nominal,
                    ]);

                    $processedCount++;
                });
            } catch (\Throwable $e) {
                Log::error('Failed to process auto withdrawal for member', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $processedCount;
    }
}
