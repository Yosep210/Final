<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('renders the role management page for authenticated members', function () {
    Role::findOrCreate('Admin', 'web');

    $member = Member::factory()->active()->create();
    $member->assignRole('Admin');

    $this->actingAs($member)
        ->get(route('role.index'))
        ->assertOk()
        ->assertSee('Role');
});
