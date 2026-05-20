<?php

use App\Models\Member;

test('confirm password screen can be rendered', function () {
    $user = Member::factory()->create();

    $response = $this->actingAs($user)->get(route('password.confirm'));

    $response->assertOk();
});
