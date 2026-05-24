<?php

use App\Models\Member;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

test('members can authenticate using the login screen', function () {
    $member = Member::factory()->create();

    $response = $this->post(route('login.store'), [
        'username' => $member->username,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('members can not authenticate with invalid password', function () {
    $member = Member::factory()->create();

    $response = $this->post(route('login.store'), [
        'username' => $member->username,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('members with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $member = Member::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'username' => $member->username,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('members can logout', function () {
    $member = Member::factory()->create();

    $response = $this->actingAs($member)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});
