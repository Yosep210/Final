<?php

use App\Actions\Member\CreateMemberAction;
use App\Data\MemberData;
use App\Models\Member;
use App\Models\MemberNetwork;
use App\Models\MemberRewardPoint;
use App\Models\ProductOrder;
use App\Models\Reward;
use App\Models\RewardPoint;
use App\Services\RewardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Setup reward configurations manually to decouple tests from external databases.
 */
function seedTestRewardConfigs(): void
{
    DB::table('reward_configs')->insert([
        [
            'id' => 1,
            'reward' => 'Handphone Android',
            'point' => 100,
            'nominal' => 1500000.00,
            'type' => 'lifetime',
            'packages' => '[]',
            'rank' => 'Gold',
            'is_lifetime' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => 2,
            'reward' => 'Laptop Core i5',
            'point' => 300,
            'nominal' => 7000000.00,
            'type' => 'lifetime',
            'packages' => '[]',
            'rank' => 'Sapphire',
            'is_lifetime' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => 3,
            'reward' => 'Sepeda Motor Honda Vario',
            'point' => 1000,
            'nominal' => 22000000.00,
            'type' => 'lifetime',
            'packages' => '[]',
            'rank' => 'Ruby',
            'is_lifetime' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
}

/**
 * Build a binary tree structure for testing.
 */
function setupTestNetworkTree(): array
{
    // Create members with controlled activation dates
    $now = Carbon::now();
    $a = Member::factory()->active()->create(['username' => 'member_a', 'created_at' => $now]);
    $b = Member::factory()->active()->create(['username' => 'member_b', 'created_at' => $now]);
    $c = Member::factory()->active()->create(['username' => 'member_c', 'created_at' => $now]);
    $d = Member::factory()->active()->create(['username' => 'member_d', 'created_at' => $now]);
    $e = Member::factory()->active()->create(['username' => 'member_e', 'created_at' => $now]);

    // Set up their networks
    $a->network()->updateOrCreate([], [
        'parent_id' => null,
        'position' => null,
        'path' => (string) $a->id,
        'generation' => 0,
        'current_rank' => 'member',
    ]);

    $b->network()->updateOrCreate([], [
        'parent_id' => $a->id,
        'position' => 'left',
        'path' => $a->id.'/'.$b->id,
        'generation' => 1,
        'current_rank' => 'member',
    ]);

    $c->network()->updateOrCreate([], [
        'parent_id' => $a->id,
        'position' => 'right',
        'path' => $a->id.'/'.$c->id,
        'generation' => 1,
        'current_rank' => 'member',
    ]);

    $d->network()->updateOrCreate([], [
        'parent_id' => $b->id,
        'position' => 'left',
        'path' => $a->id.'/'.$b->id.'/'.$d->id,
        'generation' => 2,
        'current_rank' => 'member',
    ]);

    $e->network()->updateOrCreate([], [
        'parent_id' => $b->id,
        'position' => 'right',
        'path' => $a->id.'/'.$b->id.'/'.$e->id,
        'generation' => 2,
        'current_rank' => 'member',
    ]);

    return [$a, $b, $c, $d, $e];
}

it('calculates left and right points correctly based on downline subtrees', function () {
    seedTestRewardConfigs();
    [$a, $b, $c, $d, $e] = setupTestNetworkTree();

    // Add point logs under B, C, D, E
    RewardPoint::create([
        'member_id' => $b->id,
        'package' => 'silver',
        'type' => 'ro',
        'bv' => 100,
        'point' => 50.00,
        'status' => 1,
    ]);

    RewardPoint::create([
        'member_id' => $d->id,
        'package' => 'silver',
        'type' => 'ro',
        'bv' => 100,
        'point' => 30.00,
        'status' => 1,
    ]);

    RewardPoint::create([
        'member_id' => $e->id,
        'package' => 'silver',
        'type' => 'ro',
        'bv' => 100,
        'point' => 25.00,
        'status' => 1,
    ]);

    RewardPoint::create([
        'member_id' => $c->id,
        'package' => 'silver',
        'type' => 'ro',
        'bv' => 100,
        'point' => 80.00,
        'status' => 1,
    ]);

    $service = new RewardService;
    $points = $service->calculatePoints($a);

    // Left points should be B (50) + D (30) + E (25) = 105
    expect($points['left'])->toEqual(105.00);

    // Right points should be C (80)
    expect($points['right'])->toEqual(80.00);
});

it('ignores points generated before the member activation date', function () {
    seedTestRewardConfigs();
    [$a, $b, $c, $d, $e] = setupTestNetworkTree();

    // Set A's activation date to 10 days from now
    $a->created_at = Carbon::now()->addDays(10);
    $a->save();

    // Add a point log under B created today (before A's activation date)
    DB::table('reward_points')->insert([
        'member_id' => $b->id,
        'package' => 'silver',
        'type' => 'ro',
        'bv' => 100,
        'point' => 50.00,
        'status' => 1,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);

    // Add another point log under B created 12 days from now (after A's activation date)
    DB::table('reward_points')->insert([
        'member_id' => $b->id,
        'package' => 'silver',
        'type' => 'ro',
        'bv' => 100,
        'point' => 30.00,
        'status' => 1,
        'created_at' => Carbon::now()->addDays(12),
        'updated_at' => Carbon::now()->addDays(12),
    ]);

    $service = new RewardService;
    $points = $service->calculatePoints($a);

    // B's first point log is ignored. B's second point log is counted.
    expect($points['left'])->toEqual(30.00);
});

it('awards lifetime rewards and upgrades network ranks on qualification', function () {
    seedTestRewardConfigs();
    [$a, $b, $c, $d, $e] = setupTestNetworkTree();

    // Left tree points: B (150) -> Total Left = 150
    RewardPoint::create([
        'member_id' => $b->id,
        'package' => 'silver',
        'type' => 'ro',
        'point' => 150.00,
        'status' => 1,
    ]);

    // Right tree points: C (120) -> Total Right = 120
    RewardPoint::create([
        'member_id' => $c->id,
        'package' => 'silver',
        'type' => 'ro',
        'point' => 120.00,
        'status' => 1,
    ]);

    $service = new RewardService;
    $service->processQualifications();

    // Check MemberRewardPoint summary was written
    $summary = MemberRewardPoint::where('member_id', $a->id)->first();
    expect($summary)->not->toBeNull();
    expect($summary->point_left)->toEqual(150.00);
    expect($summary->point_right)->toEqual(120.00);
    expect($summary->point_qualified)->toEqual(120.00);

    // Check reward config 1 (Handphone Android, 100 points) was awarded to A
    $reward = Reward::where('member_id', $a->id)->where('reward_config_id', 1)->first();
    expect($reward)->not->toBeNull();
    expect($reward->nominal)->toEqual(1500000.00);
    expect($reward->status)->toEqual(0); // Pending

    // Check that config 2 (Laptop, 300 points) was NOT awarded
    $laptopReward = Reward::where('member_id', $a->id)->where('reward_config_id', 2)->first();
    expect($laptopReward)->toBeNull();

    // Check that A's rank in member_networks was upgraded to 'Gold'
    $aNetwork = MemberNetwork::where('member_id', $a->id)->first();
    expect($aNetwork->current_rank)->toEqual('Gold');
});

it('enforces expiration rules on non-lifetime promo rewards', function () {
    // Insert a promo reward config (is_lifetime = false)
    DB::table('reward_configs')->insert([
        'id' => 99,
        'reward' => 'Promo Android Phone',
        'point' => 50,
        'nominal' => 2000000.00,
        'type' => 'promo',
        'packages' => '[]',
        'rank' => 'Gold',
        'is_lifetime' => false,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    [$a, $b, $c] = setupTestNetworkTree();

    // Make Member A activated 3 months ago (so promo qualification period has expired)
    $a->created_at = Carbon::now()->subMonths(3);
    $a->save();

    // Add qualified points: Left = 60, Right = 60 (qualifies for 50-point promo)
    DB::table('reward_points')->insert([
        'member_id' => $b->id,
        'package' => 'silver',
        'type' => 'ro',
        'point' => 60.00,
        'status' => 1,
        'created_at' => Carbon::now()->subMonths(2), // created after A's activation
        'updated_at' => Carbon::now()->subMonths(2),
    ]);

    DB::table('reward_points')->insert([
        'member_id' => $c->id,
        'package' => 'silver',
        'type' => 'ro',
        'point' => 60.00,
        'status' => 1,
        'created_at' => Carbon::now()->subMonths(2), // created after A's activation
        'updated_at' => Carbon::now()->subMonths(2),
    ]);

    $service = new RewardService;
    $service->processQualifications();

    // Member A should NOT be awarded the promo reward because it is expired for them
    $promoReward = Reward::where('member_id', $a->id)->where('reward_config_id', 99)->first();
    expect($promoReward)->toBeNull();
});

it('runs via the artisan command successfully', function () {
    seedTestRewardConfigs();
    [$a, $b, $c] = setupTestNetworkTree();

    RewardPoint::create([
        'member_id' => $b->id,
        'package' => 'silver',
        'type' => 'ro',
        'point' => 120.00,
        'status' => 1,
    ]);

    RewardPoint::create([
        'member_id' => $c->id,
        'package' => 'silver',
        'type' => 'ro',
        'point' => 120.00,
        'status' => 1,
    ]);

    // Run the Artisan command
    Artisan::call('reward:process-qualification');

    // Confirm that points and rewards were calculated and stored
    $summary = MemberRewardPoint::where('member_id', $a->id)->first();
    expect($summary)->not->toBeNull();
    expect($summary->point_qualified)->toEqual(120.00);

    $reward = Reward::where('member_id', $a->id)->where('reward_config_id', 1)->first();
    expect($reward)->not->toBeNull();
});

it('creates a reward point log when a member is registered/activated via CreateMemberAction', function () {
    // Setup default role and requirement configurations
    config(['mlm.registration_requires_pin' => false]);
    Role::findOrCreate('Member', 'web');

    $memberData = new MemberData(
        name: 'Jane Doe',
        username: 'janedoe',
        email: 'jane@example.com',
        password: 'password',
        status: 'active',
        referralCode: null,
        emailVerifiedAt: null,
        lastLoginAt: null,
    );

    $member = CreateMemberAction::run($memberData);

    // Confirm that a RewardPoint log was generated for Jane
    $pointLog = RewardPoint::where('member_id', $member->id)->first();
    expect($pointLog)->not->toBeNull();
    expect($pointLog->type)->toEqual('activation');
    expect($pointLog->package)->toEqual('silver');
    expect($pointLog->bv)->toEqual((int) config('mlm.commission.registration_bv', 2500));
    expect($pointLog->point)->toEqual(1.0);
    expect($pointLog->status)->toEqual(1);
});

it('creates a reward point log when a product order status is confirmed or completed', function () {
    $member = Member::factory()->active()->create();

    // Create a product order with status 0 (Review)
    $order = ProductOrder::create([
        'member_id' => $member->id,
        'invoice' => 'INV/RO/101',
        'type_order' => 'manual_ro',
        'status' => 0,
        'total_bv' => 1500,
        'point_reward' => 2.50,
    ]);

    // Verify no reward points generated yet
    expect(RewardPoint::where('product_order_id', $order->id)->exists())->toBeFalse();

    // Confirm the order (update status to 1)
    $order->update(['status' => 1]);

    // Verify reward point created
    $pointLog = RewardPoint::where('product_order_id', $order->id)->first();
    expect($pointLog)->not->toBeNull();
    expect($pointLog->member_id)->toEqual($member->id);
    expect($pointLog->type)->toEqual('ro');
    expect($pointLog->package)->toEqual('silver'); // default package
    expect($pointLog->bv)->toEqual(1500);
    expect($pointLog->point)->toEqual(2.50);
    expect($pointLog->status)->toEqual(1);

    // Let's verify updating status to 2 (Done) does not duplicate
    $order->update(['status' => 2]);
    $count = RewardPoint::where('product_order_id', $order->id)->count();
    expect($count)->toEqual(1);
});
