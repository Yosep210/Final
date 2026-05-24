<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('renders the country management page for authenticated members', function () {
    Role::findOrCreate('Admin', 'web');

    $member = Member::factory()->active()->create();
    $member->assignRole('Admin');

    $this->actingAs($member)
        ->get(route('country.index'))
        ->assertOk()
        ->assertSee('Country')
        ->assertSee('Add Country');
});
