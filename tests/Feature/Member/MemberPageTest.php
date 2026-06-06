<?php

use App\Livewire\Member\MemberTable;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

it('renders the member management page with members present', function () {
    Member::factory()->active()->create([
        'last_login_at' => null,
    ]);

    $this->actingAs(createAdminMember())
        ->get(route('member.index'))
        ->assertOk()
        ->assertSee('Member');
});

it('can search and render MemberTable with sponsor and parent information', function () {
    $admin = createAdminMember();

    $sponsor = Member::factory()->active()->create([
        'username' => 'SPONSOR1',
        'name' => 'Sponsor Name',
    ]);

    $parent = Member::factory()->active()->create([
        'username' => 'PARENT1',
        'name' => 'Parent Name',
    ]);

    $member = Member::factory()->active()->create([
        'username' => 'MEMBER1',
        'name' => 'Member Name',
        'email' => 'member1@example.com',
    ]);

    $member->network()->updateOrCreate([], [
        'sponsored_id' => $sponsor->id,
        'parent_id' => $parent->id,
        'position' => 'left',
        'current_rank' => 'star',
    ]);

    $member->profile()->updateOrCreate([], [
        'phone' => '6281234567890',
    ]);

    Livewire::actingAs($admin)
        ->test(MemberTable::class)
        ->assertSee('SPONSOR1')
        ->assertSee('PARENT1')
        ->assertSee('MEMBER1')
        ->assertSee('Star')
        ->set('filters', [
            'input_text' => [
                'contact' => 'member1@example.com',
            ],
        ])
        ->assertSee('MEMBER1');
});
