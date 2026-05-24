<?php

use App\Livewire\Settings\Profile;
use App\Models\Member;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = Member::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = Member::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = Member::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('member can delete their account', function () {
    $user = Member::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-member-form')
        ->set('password', 'password')
        ->call('deleteMember');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect(Member::query()->find($user->id))->toBeNull();
    expect(Member::withTrashed()->find($user->id))->not->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete member account', function () {
    $user = Member::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-member-form')
        ->set('password', 'wrong-password')
        ->call('deleteMember');

    $response->assertHasErrors(['password']);

    expect(Member::query()->find($user->id))->not->toBeNull();
});
