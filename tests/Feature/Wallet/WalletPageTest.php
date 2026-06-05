<?php

use App\Livewire\Wallet\Index as WalletIndex;
use App\Livewire\Wallet\WalletTable;
use App\Models\EwalletLog;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createAdminUser(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    return $admin;
}

it('redirects guests from wallet page', function () {
    $this->get(route('wallet.index'))->assertRedirect(route('login'));
});

it('forbids non-admin users from wallet page', function () {
    $nonAdmin = Member::factory()->active()->create();

    $this->actingAs($nonAdmin)->get(route('wallet.index'))->assertForbidden();
});

it('renders the wallet page for admins', function () {
    $admin = createAdminUser();

    $this->actingAs($admin)->get(route('wallet.index'))
        ->assertOk()
        ->assertSee('eWallet Logs');
});

it('aggregates and displays balances correctly', function () {
    $admin = createAdminUser();
    $member1 = Member::factory()->active()->create(['name' => 'Alice', 'username' => 'alice123']);
    $member2 = Member::factory()->active()->create(['name' => 'Bob', 'username' => 'bob456']);

    // Alice logs: IN 100000, OUT 30000 -> balance 70000
    EwalletLog::create([
        'member_id' => $member1->id,
        'nominal' => 100000,
        'amount' => 100000,
        'type' => 'IN',
        'status' => 1,
    ]);
    EwalletLog::create([
        'member_id' => $member1->id,
        'nominal' => 30000,
        'amount' => 30000,
        'type' => 'OUT',
        'status' => 1,
    ]);

    // Bob logs: IN 200000 -> balance 200000
    EwalletLog::create([
        'member_id' => $member2->id,
        'nominal' => 200000,
        'amount' => 200000,
        'type' => 'IN',
        'status' => 1,
    ]);

    Livewire::actingAs($admin)
        ->test(WalletTable::class)
        ->assertSee('ALICE123')
        ->assertSee('BOB456')
        ->assertSee('70,000')
        ->assertSee('200,000');
});

it('can open detail modal and show member wallet logs', function () {
    $admin = createAdminUser();
    $member = Member::factory()->active()->create(['name' => 'Alice', 'username' => 'alice123']);

    EwalletLog::create([
        'member_id' => $member->id,
        'nominal' => 150000,
        'amount' => 150000,
        'type' => 'IN',
        'status' => 1,
        'description' => 'Test deposit',
    ]);

    Livewire::actingAs($admin)
        ->test(WalletIndex::class)
        ->assertSet('showDetailModal', false)
        ->call('openDetail', $member->id)
        ->assertSet('showDetailModal', true)
        ->assertSet('selectedMember.id', $member->id)
        ->assertCount('memberWalletLogs', 1)
        ->assertSee('Test deposit');
});
