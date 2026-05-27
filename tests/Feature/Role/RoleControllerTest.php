<?php

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function rolePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Administrator',
        'guard_name' => 'web',
    ], $overrides);
}

function actingAsAdminForRoleApi(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    Sanctum::actingAs($admin);

    return $admin;
}

it('returns a paginated role list', function () {
    actingAsAdminForRoleApi();

    Role::query()->create(rolePayload());
    Role::query()->create(rolePayload(['name' => 'Manager']));

    $this->getJson(route('roles.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'guard_name', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('stores a role', function () {
    actingAsAdminForRoleApi();

    $this->postJson(route('roles.store'), rolePayload())
        ->assertCreated()
        ->assertJsonPath('data.name', 'Administrator');

    $this->assertDatabaseHas('roles', rolePayload());
});

it('fails to store a duplicate role', function () {
    actingAsAdminForRoleApi();
    Role::query()->create(rolePayload());

    $this->postJson(route('roles.store'), rolePayload())
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a role', function () {
    actingAsAdminForRoleApi();
    $role = Role::query()->create(rolePayload());

    $this->getJson(route('roles.show', $role))
        ->assertOk()
        ->assertJsonPath('data.id', $role->id);
});

it('updates a role', function () {
    actingAsAdminForRoleApi();
    $role = Role::query()->create(rolePayload());

    $this->putJson(route('roles.update', $role), rolePayload(['name' => 'Supervisor']))
        ->assertOk()
        ->assertJsonPath('data.name', 'Supervisor');

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => 'Supervisor',
    ]);
});

it('fails to update a role with duplicate name', function () {
    actingAsAdminForRoleApi();
    Role::query()->create(rolePayload());
    $role = Role::query()->create(rolePayload(['name' => 'Manager']));

    $this->putJson(route('roles.update', $role), rolePayload(['name' => 'Administrator']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('deletes a role', function () {
    actingAsAdminForRoleApi();
    $role = Role::query()->create(rolePayload());

    $this->deleteJson(route('roles.destroy', $role))
        ->assertNoContent();

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

it('rejects guests from listing roles', function () {
    $this->getJson(route('roles.index'))
        ->assertUnauthorized();
});

it('forbids non-admin members from listing roles', function () {
    Sanctum::actingAs(Member::factory()->active()->create());

    $this->getJson(route('roles.index'))
        ->assertForbidden();
});
