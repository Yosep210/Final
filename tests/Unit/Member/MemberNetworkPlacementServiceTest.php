<?php

use App\Models\Member;
use App\Models\MemberNetwork;
use App\Services\MemberNetworkPlacementService;

it('places a new member on the first available sponsor leg', function () {
    $sponsor = Member::factory()->active()->create([
        'referral_code' => 'SPONSOR1',
    ]);

    MemberNetwork::query()->create([
        'member_id' => $sponsor->id,
        'path' => (string) $sponsor->id,
        'generation' => 0,
        'group' => 0,
        'rank' => 0,
    ]);

    $newMember = Member::factory()->active()->create();

    $placement = app(MemberNetworkPlacementService::class)
        ->resolvePlacement($newMember, referralCode: 'SPONSOR1');

    expect($placement['sponsored_id'])->toBe($sponsor->id);
    expect($placement['parent_id'])->toBe($sponsor->id);
    expect($placement['position'])->toBe('left');
    expect($placement['generation'])->toBe(1);
    expect($placement['group'])->toBe(1);
    expect($placement['path'])->toBe($sponsor->id.'/'.$newMember->id);
    expect($placement['rank'])->toBe(1);
});

it('falls back to breadth first placement when sponsor left and right are occupied', function () {
    $sponsor = Member::factory()->active()->create([
        'referral_code' => 'SPONSOR2',
    ]);

    $leftChild = Member::factory()->active()->create();
    $rightChild = Member::factory()->active()->create();

    MemberNetwork::query()->create([
        'member_id' => $sponsor->id,
        'path' => (string) $sponsor->id,
        'generation' => 0,
        'group' => 0,
        'rank' => 0,
    ]);

    MemberNetwork::query()->create([
        'member_id' => $leftChild->id,
        'sponsored_id' => $sponsor->id,
        'parent_id' => $sponsor->id,
        'position' => 'left',
        'path' => $sponsor->id.'/'.$leftChild->id,
        'generation' => 1,
        'group' => 1,
        'rank' => 1,
    ]);

    MemberNetwork::query()->create([
        'member_id' => $rightChild->id,
        'sponsored_id' => $sponsor->id,
        'parent_id' => $sponsor->id,
        'position' => 'right',
        'path' => $sponsor->id.'/'.$rightChild->id,
        'generation' => 1,
        'group' => 2,
        'rank' => 2,
    ]);

    $newMember = Member::factory()->active()->create();

    $placement = app(MemberNetworkPlacementService::class)
        ->resolvePlacement($newMember, referralCode: 'SPONSOR2');

    expect($placement['sponsored_id'])->toBe($sponsor->id);
    expect($placement['parent_id'])->toBe($leftChild->id);
    expect($placement['position'])->toBe('left');
    expect($placement['generation'])->toBe(2);
    expect($placement['group'])->toBe(1);
    expect($placement['path'])->toBe($sponsor->id.'/'.$leftChild->id.'/'.$newMember->id);
});

it('honors an explicit parent position when that slot is still empty', function () {
    $sponsor = Member::factory()->active()->create();
    $parent = Member::factory()->active()->create();

    MemberNetwork::query()->create([
        'member_id' => $sponsor->id,
        'path' => (string) $sponsor->id,
        'generation' => 0,
        'group' => 0,
        'rank' => 0,
    ]);

    MemberNetwork::query()->create([
        'member_id' => $parent->id,
        'sponsored_id' => $sponsor->id,
        'parent_id' => $sponsor->id,
        'position' => 'right',
        'path' => $sponsor->id.'/'.$parent->id,
        'generation' => 1,
        'group' => 2,
        'rank' => 2,
    ]);

    $newMember = Member::factory()->active()->create();

    $placement = app(MemberNetworkPlacementService::class)
        ->resolvePlacement(
            $newMember,
            explicitSponsorId: $sponsor->id,
            explicitParentId: $parent->id,
            position: 'right',
        );

    expect($placement['sponsored_id'])->toBe($sponsor->id);
    expect($placement['parent_id'])->toBe($parent->id);
    expect($placement['position'])->toBe('right');
    expect($placement['generation'])->toBe(2);
    expect($placement['group'])->toBe(2);
    expect($placement['path'])->toBe($sponsor->id.'/'.$parent->id.'/'.$newMember->id);
    expect($placement['rank'])->toBe(2);
});
