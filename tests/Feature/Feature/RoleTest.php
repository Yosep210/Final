<?php

use App\Models\Role;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('role index success', function () {
    $this->withoutMiddleware();

    Role::factory()->create();

    $response = $this->getJson('/admin/roles');

    $response->assertStatus(200);
});

test('role create success', function () {
    $this->withoutMiddleware();

    $data = [
        'name' => 'Test Role',
        'guard_name' => 'web',
    ];

    $response = $this->postJson('/admin/roles', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('roles', $data);
});

test('role create duplicate fails', function () {
    $this->withoutMiddleware();

    Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

    $data = [
        'name' => 'Test Role',
        'guard_name' => 'web',
    ];

    $response = $this->postJson('/admin/roles', $data);

    $response->assertStatus(422);
});

test('role show success', function () {
    $this->withoutMiddleware();

    $role = Role::factory()->create();

    $response = $this->getJson("/admin/roles/{$role->id}");

    $response->assertStatus(200);
});

test('role update success', function () {
    $this->withoutMiddleware();

    $role = Role::factory()->create();

    $data = [
        'name' => 'Updated Role',
        'guard_name' => 'web',
    ];

    $response = $this->putJson("/admin/roles/{$role->id}", $data);

    $response->assertStatus(200);
    $this->assertDatabaseHas('roles', $data);
});

test('role update duplicate fails', function () {
    $this->withoutMiddleware();

    Role::create(['name' => 'Existing Role', 'guard_name' => 'web']);
    $role = Role::factory()->create();

    $data = [
        'name' => 'Existing Role',
        'guard_name' => 'web',
    ];

    $response = $this->putJson("/admin/roles/{$role->id}", $data);

    $response->assertStatus(422);
});

test('role delete success', function () {
    $this->withoutMiddleware();

    $role = Role::factory()->create();

    $response = $this->deleteJson("/admin/roles/{$role->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});
