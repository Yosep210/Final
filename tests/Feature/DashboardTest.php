<?php

use App\Models\Member;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated members can visit the dashboard', function () {
    $member = Member::factory()->create();
    $this->actingAs($member);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
