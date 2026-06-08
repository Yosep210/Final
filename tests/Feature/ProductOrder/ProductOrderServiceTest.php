<?php

use App\Models\Member;
use App\Services\ProductOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new ProductOrderService;

    // Setup clear configurations for the tests
    config([
        'mlm.stockist.minimum_order' => [
            1 => 7500000.00,  // Mobile Stockist
            2 => 7500000.00,  // Stockist
            3 => 90000000.00, // Master Stockist
        ],
        'mlm.stockist.minimum_order_by_member_id' => [
            2052 => 54000000.00, // Specific member override
        ],
        'mlm.stockist.discount' => [
            1 => 5.0,  // 5% max for Mobile Stockist
            3 => 15.0, // 15% max for Master Stockist
        ],
        'mlm.stockist.minimum_order_discount' => [
            5000000 => 2.0,   // 2% for >= 5m
            25000000 => 10.0, // 10% for >= 25m
            150000000 => 20.0, // 20% for >= 150m
        ],
    ]);
});

it('exempts regular members from minimum order requirements', function () {
    $member = Member::factory()->create(['type' => 0]); // Regular member

    $result = $this->service->validateCheckoutMinimumOrder($member, 100000.00);

    expect($result['valid'])->toBeTrue()
        ->and($result['min_required'])->toBe(0.0);
});

it('enforces minimum order requirements for mobile stockists', function () {
    $member = Member::factory()->create(['type' => 1]); // Mobile Stockist

    // Subtotal under 7.5m should fail
    $resultFail = $this->service->validateCheckoutMinimumOrder($member, 5000000.00);
    expect($resultFail['valid'])->toBeFalse()
        ->and($resultFail['min_required'])->toBe(7500000.00)
        ->and($resultFail['message'])->toContain('Rp 7.500.000');

    // Subtotal of 7.5m should pass
    $resultPass = $this->service->validateCheckoutMinimumOrder($member, 7500000.00);
    expect($resultPass['valid'])->toBeTrue();
});

it('enforces override minimum order for specific member IDs', function () {
    // Member with ID 2052 (configured override to 54m)
    $member = Member::factory()->create([
        'id' => 2052,
        'type' => 3, // Master stockist (normally 90m)
    ]);

    // Subtotal 50m should fail (since limit is overridden to 54m)
    $resultFail = $this->service->validateCheckoutMinimumOrder($member, 50000000.00);
    expect($resultFail['valid'])->toBeFalse()
        ->and($resultFail['min_required'])->toBe(54000000.00);

    // Subtotal 55m should pass
    $resultPass = $this->service->validateCheckoutMinimumOrder($member, 55000000.00);
    expect($resultPass['valid'])->toBeTrue();
});

it('returns zero discount for regular members', function () {
    $member = Member::factory()->create(['type' => 0]);

    $discount = $this->service->calculateStockistDiscount($member, 10000000.00);

    expect($discount)->toBe(0.0);
});

it('calculates tiered discount capped at the stockist base type discount limit', function () {
    // Mobile stockist has max 5% base discount
    $mobileStockist = Member::factory()->create(['type' => 1]);

    // Order 6m qualifies for 2% tier. 2% < 5% limit -> should get 2% discount.
    $discountSmall = $this->service->calculateStockistDiscount($mobileStockist, 6000000.00);
    expect($discountSmall)->toBe(120000.00); // 2% of 6,000,000

    // Order 30m qualifies for 10% tier. 10% > 5% limit -> capped at 5% -> should get 5% discount.
    $discountLarge = $this->service->calculateStockistDiscount($mobileStockist, 30000000.00);
    expect($discountLarge)->toBe(1500000.00); // 5% of 30,000,000
});

it('allows master stockists to get higher tiered discounts up to their cap', function () {
    // Master stockist has max 15% base discount
    $masterStockist = Member::factory()->create(['type' => 3]);

    // Order 30m qualifies for 10% tier. 10% < 15% limit -> should get 10% discount.
    $discountMid = $this->service->calculateStockistDiscount($masterStockist, 30000000.00);
    expect($discountMid)->toBe(3000000.00); // 10% of 30,000,000

    // Order 160m qualifies for 20% tier. 20% > 15% limit -> capped at 15% -> should get 15% discount.
    $discountLarge = $this->service->calculateStockistDiscount($masterStockist, 160000000.00);
    expect($discountLarge)->toBe(24000000.00); // 15% of 160,000,000
});
