<?php

use App\Livewire\AutoRo\AutoRoTable;
use App\Livewire\AutoRo\Index as AutoRoIndex;
use App\Models\AutoRoLog;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createAdminForAutoRo(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    return $admin;
}

it('redirects guests from auto ro page', function () {
    $this->get(route('auto.ro.index'))->assertRedirect(route('login'));
});

it('forbids non-admin users from auto ro page', function () {
    $nonAdmin = Member::factory()->active()->create();

    $this->actingAs($nonAdmin)->get(route('auto.ro.index'))->assertForbidden();
});

it('renders the auto ro page for admins', function () {
    $admin = createAdminForAutoRo();

    $this->actingAs($admin)->get(route('auto.ro.index'))
        ->assertOk()
        ->assertSee('Auto RO Logs');
});

it('aggregates and displays Auto RO balances correctly', function () {
    $admin = createAdminForAutoRo();
    $member1 = Member::factory()->active()->create(['name' => 'Alice', 'username' => 'alice123']);
    $member2 = Member::factory()->active()->create(['name' => 'Bob', 'username' => 'bob456']);

    // Alice logs: 500000 + 700000 = 1200000
    AutoRoLog::create([
        'member_id' => $member1->id,
        'nominal' => 500000,
        'amount' => 500000,
        'status' => 1,
    ]);
    AutoRoLog::create([
        'member_id' => $member1->id,
        'nominal' => 700000,
        'amount' => 700000,
        'status' => 1,
    ]);

    // Bob logs: 2000000 - 1000000 (deduction) = 1000000
    AutoRoLog::create([
        'member_id' => $member2->id,
        'nominal' => 2000000,
        'amount' => 2000000,
        'status' => 1,
    ]);
    AutoRoLog::create([
        'member_id' => $member2->id,
        'nominal' => 1000000,
        'amount' => -1000000,
        'status' => 1,
    ]);

    Livewire::actingAs($admin)
        ->test(AutoRoTable::class)
        ->assertSee('ALICE123')
        ->assertSee('BOB456')
        ->assertSee('1,200,000')
        ->assertSee('1,000,000');
});

it('can open Auto RO detail modal and show member logs', function () {
    $admin = createAdminForAutoRo();
    $member = Member::factory()->active()->create(['name' => 'Alice', 'username' => 'alice123']);

    AutoRoLog::create([
        'member_id' => $member->id,
        'nominal' => 1500000,
        'amount' => 1500000,
        'status' => 1,
        'description' => 'Test Auto RO entry',
    ]);

    Livewire::actingAs($admin)
        ->test(AutoRoIndex::class)
        ->assertSet('showDetailModal', false)
        ->call('openDetail', $member->id)
        ->assertSet('showDetailModal', true)
        ->assertSet('selectedMember.id', $member->id)
        ->assertCount('memberAutoRoLogs', 1)
        ->assertSee('Test Auto RO entry');
});
