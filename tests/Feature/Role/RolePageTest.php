<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the role management page for authenticated users', function () {
    $this->actingAs(Member::factory()->create())
        ->get(route('role.index'))
        ->assertOk()
        ->assertSee('Role');
});
