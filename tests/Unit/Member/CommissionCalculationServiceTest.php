<?php

use App\Models\AutoRoLog;
use App\Models\CommissionLog;
use App\Models\CommissionPayout;
use App\Models\Member;
use App\Models\MemberNetwork;
use App\Models\Pin;
use App\Services\CommissionCalculationService;

it('calculates binary commission from network volumes using configured rates', function () {
    config()->set('mlm.commission.minimum_volume', 100);
    config()->set('mlm.commission.rates.silver', 7);
    config()->set('mlm.commission.tax_rate', 10);

    $member = Member::factory()->active()->create();

    MemberNetwork::query()->create([
        'member_id' => $member->id,
        'left_volume' => 500,
        'right_volume' => 300,
        'current_rank' => 'silver',
    ]);

    $service = app(CommissionCalculationService::class);
    $commission = $service->calculateBinaryCommission($member->fresh('network'), 2026, 5);

    expect($commission)->not->toBeNull();
    expect((float) $commission->matched_volume)->toBe(300.0);
    expect((float) $commission->gross_commission)->toBe(21.0);
    expect((float) $commission->tax_amount)->toBe(2.1);
    expect((float) $commission->net_commission)->toBe(18.9);
    expect((float) $commission->commission_rate)->toBe(7.0);
    expect((float) $commission->tax_rate)->toBe(10.0);
    expect($commission->member_rank)->toBe('silver');

    $updatedNetwork = $member->fresh('network')->network;
    expect((float) $updatedNetwork->left_volume)->toBe(200.0);
    expect((float) $updatedNetwork->right_volume)->toBe(0.0);

    expect(CommissionLog::query()->count())->toBe(1);
});

it('returns the existing commission log instead of duplicating the same period', function () {
    $member = Member::factory()->active()->create();

    MemberNetwork::query()->create([
        'member_id' => $member->id,
        'left_volume' => 200,
        'right_volume' => 200,
        'current_rank' => 'member',
    ]);

    $service = app(CommissionCalculationService::class);

    $first = $service->calculateBinaryCommission($member->fresh('network'), 2026, 5);
    $second = $service->calculateBinaryCommission($member->fresh('network'), 2026, 5);

    expect($first)->not->toBeNull();
    expect($second)->not->toBeNull();
    expect($second->id)->toBe($first->id);
    expect(CommissionLog::query()->count())->toBe(1);
});

it('creates or updates payout totals while preserving prior paid amounts', function () {
    $member = Member::factory()->active()->create();

    CommissionLog::query()->create([
        'member_id' => $member->id,
        'type' => 'binary',
        'source' => 'network_volume',
        'left_volume' => 500,
        'right_volume' => 500,
        'matched_volume' => 500,
        'gross_commission' => 25,
        'tax_amount' => 2.5,
        'net_commission' => 22.5,
        'commission_rate' => 5,
        'tax_rate' => 10,
        'member_rank' => 'bronze',
        'commission_year' => 2026,
        'commission_month' => 5,
    ]);

    CommissionLog::query()->create([
        'member_id' => $member->id,
        'type' => 'binary',
        'source' => 'network_volume',
        'left_volume' => 300,
        'right_volume' => 300,
        'matched_volume' => 300,
        'gross_commission' => 15,
        'tax_amount' => 1.5,
        'net_commission' => 13.5,
        'commission_rate' => 5,
        'tax_rate' => 10,
        'member_rank' => 'bronze',
        'commission_year' => 2026,
        'commission_month' => 5,
    ]);

    CommissionPayout::query()->create([
        'member_id' => $member->id,
        'total_amount' => 10,
        'amount_paid' => 12,
        'amount_remaining' => 0,
        'payout_year' => 2026,
        'payout_month' => 5,
        'status' => 'partial',
    ]);

    $service = app(CommissionCalculationService::class);
    $payout = $service->createOrUpdatePayout($member, 2026, 5);

    expect($payout)->not->toBeNull();
    expect((float) $payout->total_amount)->toBe(36.0);
    expect((float) $payout->amount_paid)->toBe(12.0);
    expect((float) $payout->fresh()->amount_remaining)->toBe(24.0);
    expect($payout->status)->toBe('pending');
});

it('applies monthly pairing capping limits based on member rank', function () {
    config()->set('mlm.commission.minimum_volume', 100);
    config()->set('mlm.commission.rates.silver', 10);
    config()->set('mlm.commission.pairing_caps.silver', 500); // Low cap for testing
    config()->set('mlm.commission.tax_rate', 10);

    $member = Member::factory()->active()->create();

    MemberNetwork::query()->create([
        'member_id' => $member->id,
        'left_volume' => 10000,
        'right_volume' => 10000,
        'current_rank' => 'silver',
    ]);

    $service = app(CommissionCalculationService::class);
    $commission = $service->calculateBinaryCommission($member->fresh('network'), 2026, 5);

    // Without cap: matched volume = 10000, rate = 10% -> gross commission = 1000
    // With cap: gross commission = 500
    // Tax: 10% of 500 = 50
    // Net: 500 - 50 = 450
    expect($commission)->not->toBeNull();
    expect((float) $commission->gross_commission)->toBe(500.0);
    expect((float) $commission->tax_amount)->toBe(50.0);
    expect((float) $commission->net_commission)->toBe(450.0);
});

it('triggers automatic repeat order and generates pin when auto-ro balance reaches threshold', function () {
    config()->set('mlm.commission.minimum_volume', 100);
    config()->set('mlm.commission.rates.silver', 10);
    config()->set('mlm.commission.tax_rate', 0); // No tax for simpler math
    config()->set('mlm.auto_ro.package_price_threshold', 1000.00); // Low threshold for test

    $member = Member::factory()->active()->create();

    MemberNetwork::query()->create([
        'member_id' => $member->id,
        'left_volume' => 50000,
        'right_volume' => 50000,
        'current_rank' => 'silver',
    ]);

    $service = app(CommissionCalculationService::class);

    // We calculate commission:
    // Left = 50000, Right = 50000 -> matched = 50000
    // Gross commission = 50000 * 10% = 5000
    // Pairing cap is default 1,000,000 so gross is 5000.
    // Net commission = 5000.
    // Ewallet split = 80% (4000). Auto-RO split = 20% (1000).
    // Balance Auto-RO reaches 1000, which matches the threshold!
    // Auto-RO should trigger, deducting 1000 from AutoRoLog and creating 1 unused PIN for the member.
    $commission = $service->calculateBinaryCommission($member->fresh('network'), 2026, 5);

    expect($commission)->not->toBeNull();

    // Verify AutoRoLog transactions:
    // 1. One transaction of +1000 from pairing split
    // 2. One transaction of -1000 from auto-repeat-order
    // Total sum = 0
    $autoRoLogs = AutoRoLog::where('member_id', $member->id)->get();
    expect($autoRoLogs->count())->toBe(2);
    expect((float) $autoRoLogs->where('amount', '>', 0)->first()->amount)->toBe(1000.0);
    expect((float) $autoRoLogs->where('amount', '<', 0)->first()->amount)->toBe(-1000.0);
    expect((float) AutoRoLog::where('member_id', $member->id)->sum('amount'))->toBe(0.0);

    // Verify PIN is generated
    $pins = Pin::where('owner_id', $member->id)->get();
    expect($pins->count())->toBe(1);
    expect($pins->first()->status)->toBe('unused');
    expect($pins->first()->serial_number)->toStartWith('RO-');
});
