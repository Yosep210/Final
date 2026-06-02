<?php

use App\Models\Member;
use App\Models\Pin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function actingAsAdmin(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    Sanctum::actingAs($admin);

    return $admin;
}

function memberPayload(array $overrides = []): array
{
    $pin = Pin::firstOrCreate(
        ['serial_number' => 'SERIAL123'],
        [
            'pin_code' => 'CODE123',
            'status' => 'unused',
        ]
    );
    $pin->update(['status' => 'unused']);

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
        'pin_serial' => 'SERIAL123',
        'pin_code' => 'CODE123',
    ], $overrides);
}

it('returns a paginated member list', function () {
    actingAsAdmin();

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
    actingAsAdmin();

    $this->postJson(route('members.store'), memberPayload())
        ->assertCreated()
        ->assertJsonPath('data.username', 'johndoe');

    $this->assertDatabaseHas('members', [
        'username' => 'johndoe',
        'email' => 'john@example.com',
    ]);
});

it('fails to store a duplicate member username', function () {
    actingAsAdmin();
    Member::factory()->create(['username' => 'johndoe', 'email' => 'other@example.com']);

    $this->postJson(route('members.store'), memberPayload())
        ->assertStatus(422)
        ->assertJsonValidationErrors(['username']);
});

it('shows a member', function () {
    actingAsAdmin();
    $member = Member::factory()->create();

    $this->getJson(route('members.show', $member))
        ->assertOk()
        ->assertJsonPath('data.id', $member->id);
});

it('updates a member', function () {
    actingAsAdmin();
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
    actingAsAdmin();
    $member = Member::factory()->create();

    $this->deleteJson(route('members.destroy', $member))
        ->assertNoContent();

    $this->assertSoftDeleted('members', ['id' => $member->id]);
});

it('rejects guests from listing members', function () {
    $this->getJson(route('members.index'))
        ->assertUnauthorized();
});

it('allows non-admin active users to list members', function () {
    Sanctum::actingAs(Member::factory()->active()->create());

    $this->getJson(route('members.index'))
        ->assertOk();
});

it('forbids non-admin users from storing members', function () {
    Sanctum::actingAs(Member::factory()->active()->create());

    $this->postJson(route('members.store'), memberPayload())
        ->assertForbidden();
});
