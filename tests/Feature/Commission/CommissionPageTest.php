<?php

use App\Livewire\Commission\CommissionTable;
use App\Models\CommissionLog;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createAdmin(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    return $admin;
}

it('redirects guests from commission submenu pages', function () {
    $this->get(route('commission.index'))->assertRedirect(route('login'));
    $this->get(route('commission.statement'))->assertRedirect(route('login'));
    $this->get(route('wallet.index'))->assertRedirect(route('login'));
    $this->get(route('auto.ro.index'))->assertRedirect(route('login'));
    $this->get(route('withdraw.index'))->assertRedirect(route('login'));
});

it('forbids non-admin users from commission submenu pages', function () {
    $nonAdmin = Member::factory()->active()->create();

    $this->actingAs($nonAdmin)->get(route('commission.index'))->assertForbidden();
    $this->actingAs($nonAdmin)->get(route('commission.statement'))->assertForbidden();
    $this->actingAs($nonAdmin)->get(route('wallet.index'))->assertForbidden();
    $this->actingAs($nonAdmin)->get(route('auto.ro.index'))->assertForbidden();
    $this->actingAs($nonAdmin)->get(route('withdraw.index'))->assertForbidden();
});

it('renders the commission submenu pages for admins', function () {
    $admin = createAdmin();

    $this->actingAs($admin)->get(route('commission.index'))->assertOk()->assertSee('Commission List');
    $this->actingAs($admin)->get(route('commission.statement'))->assertOk()->assertSee('Statement Commission');
    $this->actingAs($admin)->get(route('wallet.index'))->assertOk()->assertSee('eWallet Logs');
    $this->actingAs($admin)->get(route('auto.ro.index'))->assertOk()->assertSee('Auto RO Logs');
    $this->actingAs($admin)->get(route('withdraw.index'))->assertOk()->assertSee('Withdraw List');
});

it('renders CommissionTable and filters by total gross commission', function () {
    $admin = createAdmin();
    $member = Member::factory()->active()->create(['username' => 'earner123', 'name' => 'John Earner']);

    CommissionLog::create([
        'member_id' => $member->id,
        'gross_commission' => 250000.00,
        'type' => 'pairing',
        'commission_year' => 2026,
        'commission_month' => 6,
    ]);

    CommissionLog::create([
        'member_id' => $member->id,
        'gross_commission' => 750000.00,
        'type' => 'sponsor',
        'commission_year' => 2026,
        'commission_month' => 6,
    ]);

    $member2 = Member::factory()->active()->create(['username' => 'bob456', 'name' => 'Bob Earner']);
    CommissionLog::create([
        'member_id' => $member2->id,
        'gross_commission' => 500000.00,
        'type' => 'sponsor',
        'commission_year' => 2026,
        'commission_month' => 6,
    ]);

    Livewire::actingAs($admin)
        ->test(CommissionTable::class)
        ->assertSee('EARNER123')
        ->assertSee('John Earner')
        ->assertSee('1,000,000')
        ->assertSee('BOB456')
        ->assertSee('Bob Earner')
        ->assertSee('500,000')
        ->set('filters', [
            'input_text' => [
                'gross_commission_formatted' => '1000000',
            ],
        ])
        ->assertSee('John Earner')
        ->assertDontSee('Bob Earner');
});
