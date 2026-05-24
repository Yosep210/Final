<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createAdminMember(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    return $admin;
}

it('redirects guests from the member management page', function () {
    $this->get(route('member.index'))
        ->assertRedirect(route('login'));
});

it('forbids non-admin users from the member management page', function () {
    $this->actingAs(Member::factory()->active()->create())
        ->get(route('member.index'))
        ->assertOk()
        ->assertDontSee('Add Member');
});

it('renders the member management page for admins', function () {
    $this->actingAs(createAdminMember())
        ->get(route('member.index'))
        ->assertOk()
        ->assertSee('Member');
});
