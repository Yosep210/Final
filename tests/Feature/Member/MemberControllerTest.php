<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function memberPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'status' => 'active',
        'referral_code' => 'REF001',
        'email_verified_at' => null,
        'last_login_at' => null,
    ], $overrides);
}

it('returns a paginated member list', function () {
    Sanctum::actingAs(Member::factory()->active()->create());

    Member::factory()->create(['username' => 'member1', 'email' => 'member1@example.com']);
    Member::factory()->create(['username' => 'member2', 'email' => 'member2@example.com']);

    $this->getJson(route('members.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'username', 'email', 'status', 'referral_code', 'email_verified_at', 'last_login_at', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('stores a member', function () {
    Sanctum::actingAs(Member::factory()->active()->create());

    $this->postJson(route('members.store'), memberPayload())
        ->assertCreated()
        ->assertJsonPath('data.username', 'johndoe');

    $this->assertDatabaseHas('members', [
        'username' => 'johndoe',
        'email' => 'john@example.com',
    ]);
});

it('fails to store a duplicate member username', function () {
    Sanctum::actingAs(Member::factory()->active()->create());
    Member::factory()->create(['username' => 'johndoe', 'email' => 'other@example.com']);

    $this->postJson(route('members.store'), memberPayload())
        ->assertStatus(422)
        ->assertJsonValidationErrors(['username']);
});

it('shows a member', function () {
    Sanctum::actingAs(Member::factory()->active()->create());
    $member = Member::factory()->create();

    $this->getJson(route('members.show', $member))
        ->assertOk()
        ->assertJsonPath('data.id', $member->id);
});

it('updates a member', function () {
    Sanctum::actingAs(Member::factory()->active()->create());
    $member = Member::factory()->create();

    $this->putJson(route('members.update', $member), memberPayload([
        'username' => $member->username,
        'email' => $member->email,
        'password' => null,
        'password_confirmation' => null,
        'name' => 'Updated Member',
        'referral_code' => 'REF002',
    ]))
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Member');

    $this->assertDatabaseHas('members', [
        'id' => $member->id,
        'name' => 'Updated Member',
    ]);
});

it('deletes a member', function () {
    Sanctum::actingAs(Member::factory()->active()->create());
    $member = Member::factory()->create();

    $this->deleteJson(route('members.destroy', $member))
        ->assertNoContent();

    $this->assertSoftDeleted('members', ['id' => $member->id]);
});
