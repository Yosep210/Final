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

it('returns a paginated role list', function () {
    Sanctum::actingAs(Member::factory()->create());

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
    Sanctum::actingAs(Member::factory()->create());

    $this->postJson(route('roles.store'), rolePayload())
        ->assertCreated()
        ->assertJsonPath('data.name', 'Administrator');

    $this->assertDatabaseHas('roles', rolePayload());
});

it('fails to store a duplicate role', function () {
    Sanctum::actingAs(Member::factory()->create());
    Role::query()->create(rolePayload());

    $this->postJson(route('roles.store'), rolePayload())
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('shows a role', function () {
    Sanctum::actingAs(Member::factory()->create());
    $role = Role::query()->create(rolePayload());

    $this->getJson(route('roles.show', $role))
        ->assertOk()
        ->assertJsonPath('data.id', $role->id);
});

it('updates a role', function () {
    Sanctum::actingAs(Member::factory()->create());
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
    Sanctum::actingAs(Member::factory()->create());
    Role::query()->create(rolePayload());
    $role = Role::query()->create(rolePayload(['name' => 'Manager']));

    $this->putJson(route('roles.update', $role), rolePayload(['name' => 'Administrator']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('deletes a role', function () {
    Sanctum::actingAs(Member::factory()->create());
    $role = Role::query()->create(rolePayload());

    $this->deleteJson(route('roles.destroy', $role))
        ->assertNoContent();

    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});
