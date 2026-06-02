<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
