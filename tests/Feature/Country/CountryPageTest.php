<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the country management page for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('country.index'))
        ->assertOk()
        ->assertSee('Country')
        ->assertSee('Add Country');
});
