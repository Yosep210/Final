<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createAdminMemberForSponsor(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    return $admin;
}

it('redirects guests from the sponsor list page', function () {
    $this->get(route('sponsor.index'))
        ->assertRedirect(route('login'));
});

it('renders the sponsor list page for admins', function () {
    $admin = createAdminMemberForSponsor();

    // Create a couple of members
    $member1 = Member::factory()->active()->create(['username' => 'member1']);
    $member2 = Member::factory()->active()->create(['username' => 'member2']);

    // Setup network so admin is sponsor of member1, and member1 is sponsor of member2
    $member1->network()->updateOrCreate([], [
        'sponsored_id' => $admin->id,
    ]);
    $member2->network()->updateOrCreate([], [
        'sponsored_id' => $member1->id,
    ]);

    $this->actingAs($admin)
        ->get(route('sponsor.index'))
        ->assertOk()
        ->assertSee('Sponsor List');
});
