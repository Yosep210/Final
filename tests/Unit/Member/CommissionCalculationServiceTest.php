<?php

use App\Models\CommissionLog;
use App\Models\CommissionPayout;
use App\Models\Member;
use App\Models\MemberNetwork;
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
