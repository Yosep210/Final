<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CommissionSystemSeeder extends Seeder
{
    use HasSourceConnection;

    public function run(): void
    {
        $this->configureSourceConnection();

        $existingMemberIds = DB::table('members')->pluck('id')->all();
        $memberIdsMap = array_flip($existingMemberIds);

        $now = Carbon::now();

        // 1. Seed commission_logs from jpb_bonus
        $this->command?->info('Seeding commission_logs...');
        $sourceBonuses = DB::connection('latihan')
            ->table('jpb_bonus')
            ->orderBy('id')
            ->get();

        foreach ($sourceBonuses as $bonus) {
            $memberId = (int) $bonus->id_member;
            if (! isset($memberIdsMap[$memberId])) {
                continue;
            }

            // Map type int to string
            $typeStr = match ((int) $bonus->type) {
                1 => 'sponsor',
                2 => 'pairing',
                3 => 'unilevel',
                default => 'other',
            };

            $createdAt = $bonus->datecreated ? Carbon::parse($bonus->datecreated) : $now;
            $updatedAt = $bonus->datemodified ? Carbon::parse($bonus->datemodified) : $createdAt;

            // Extract month and year
            $year = $createdAt->year;
            $month = $createdAt->month;

            DB::table('commission_logs')->updateOrInsert(
                ['id' => $bonus->id],
                [
                    'id' => $bonus->id,
                    'member_id' => $memberId,
                    'type' => $typeStr,
                    'source' => $bonus->id_bonus,
                    'gross_commission' => $bonus->amount,
                    'tax_amount' => $bonus->tax,
                    'net_commission' => $bonus->amount_tax,
                    'commission_year' => $year,
                    'commission_month' => $month,
                    'notes' => $bonus->desc,
                    'is_paid' => (int) $bonus->status === 1,
                    'paid_at' => (int) $bonus->status === 1 ? $updatedAt : null,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }

        // 2. Seed ewallet_logs from jpb_ewallet
        $this->command?->info('Seeding ewallet_logs...');
        $sourceEwallet = DB::connection('latihan')
            ->table('jpb_ewallet')
            ->orderBy('id')
            ->get();

        foreach ($sourceEwallet as $wallet) {
            $memberId = (int) $wallet->id_member;
            if (! isset($memberIdsMap[$memberId])) {
                continue;
            }

            $createdAt = $wallet->datecreated ? Carbon::parse($wallet->datecreated) : $now;

            DB::table('ewallet_logs')->updateOrInsert(
                ['id' => $wallet->id],
                [
                    'id' => $wallet->id,
                    'member_id' => $memberId,
                    'source_id' => $wallet->id_source ?: null,
                    'source' => $wallet->source,
                    'category' => $wallet->category,
                    'nominal' => $wallet->nominal,
                    'percent' => $wallet->percent,
                    'autoro' => $wallet->autoro,
                    'tax' => $wallet->tax,
                    'amount' => $wallet->amount,
                    'type' => $wallet->type,
                    'status' => $wallet->status,
                    'description' => $wallet->description,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }

        // 3. Seed auto_ro_logs from jpb_auto_ro
        $this->command?->info('Seeding auto_ro_logs...');
        $sourceAutoRo = DB::connection('latihan')
            ->table('jpb_auto_ro')
            ->orderBy('id')
            ->get();

        foreach ($sourceAutoRo as $ro) {
            $memberId = (int) $ro->id_member;
            if (! isset($memberIdsMap[$memberId])) {
                continue;
            }

            $createdAt = $ro->datecreated ? Carbon::parse($ro->datecreated) : $now;

            DB::table('auto_ro_logs')->updateOrInsert(
                ['id' => $ro->id],
                [
                    'id' => $ro->id,
                    'member_id' => $memberId,
                    'source_id' => $ro->id_source ?: null,
                    'source' => $ro->source,
                    'nominal' => $ro->nominal,
                    'percent' => $ro->percent,
                    'amount' => $ro->amount,
                    'status' => $ro->status,
                    'description' => $ro->description,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }

        // 4. Seed withdrawals from jpb_withdraw
        $this->command?->info('Seeding withdrawals...');
        $sourceWithdraw = DB::connection('latihan')
            ->table('jpb_withdraw')
            ->orderBy('id')
            ->get();

        foreach ($sourceWithdraw as $w) {
            $memberId = (int) $w->id_member;
            if (! isset($memberIdsMap[$memberId])) {
                continue;
            }

            $createdAt = $w->datecreated ? Carbon::parse($w->datecreated) : $now;
            $updatedAt = $w->datemodified ? Carbon::parse($w->datemodified) : $createdAt;
            $confirmedAt = $w->dateconfirmed ? Carbon::parse($w->dateconfirmed) : null;

            DB::table('withdrawals')->updateOrInsert(
                ['id' => $w->id],
                [
                    'id' => $w->id,
                    'member_id' => $memberId,
                    'type' => $w->type,
                    'bank_name' => $w->bank_name,
                    'bank_code' => $w->bank_code,
                    'account_number' => $w->bill,
                    'account_holder' => $w->bill_name,
                    'nominal' => $w->nominal,
                    'nominal_receipt' => $w->nominal_receipt,
                    'tax' => $w->tax,
                    'auto_ro' => $w->auto_ro,
                    'admin_fund' => $w->admin_fund,
                    'status' => $w->status,
                    'flip_id' => $w->flip_id,
                    'linkita_inquiry' => $w->linkita_inquiry,
                    'inquiry_status' => $w->inquiry_status,
                    'linkita_pay' => $w->linkita_pay,
                    'payment_status' => $w->payment_status,
                    'confirmed_at' => $confirmedAt,
                    'confirmed_by' => $w->confirm_by,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }

        // 5. Generate commission_payouts from commission_logs
        $this->command?->info('Generating commission_payouts...');
        $payoutGroups = DB::table('commission_logs')
            ->select('member_id', 'commission_year', 'commission_month', DB::raw('SUM(gross_commission) as total_gross'), DB::raw('SUM(net_commission) as total_net'), DB::raw('MAX(is_paid) as has_paid'))
            ->groupBy('member_id', 'commission_year', 'commission_month')
            ->get();

        foreach ($payoutGroups as $group) {
            $memberId = (int) $group->member_id;
            $year = (int) $group->commission_year;
            $month = (int) $group->commission_month;

            DB::table('commission_payouts')->updateOrInsert(
                [
                    'member_id' => $memberId,
                    'payout_year' => $year,
                    'payout_month' => $month,
                ],
                [
                    'total_amount' => $group->total_gross,
                    'amount_paid' => $group->has_paid ? $group->total_net : 0,
                    'amount_remaining' => $group->has_paid ? 0 : $group->total_gross,
                    'status' => $group->has_paid ? 'completed' : 'pending',
                    'payment_method' => 'bank_transfer',
                    'transaction_ref' => 'TXN-'.$year.sprintf('%02d', $month).'-'.$memberId,
                    'payout_date' => $group->has_paid ? Carbon::create($year, $month, 28, 12, 0, 0) : null,
                    'created_at' => Carbon::create($year, $month, 28, 12, 0, 0),
                    'updated_at' => Carbon::create($year, $month, 28, 12, 0, 0),
                ]
            );
        }
    }
}
