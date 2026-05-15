<?php

use App\Domain\Role\Data\RoleData;
use App\Models\Role;

test('role data normalization', function () {
    $data = [
        'name' => '  Admin  ',
        'guard_name' => '  web  ',
    ];

    $roleData = RoleData::fromArray($data);

    expect($roleData->name)->toBe('Admin');
    expect($roleData->guard_name)->toBe('web');
});

test('role data from model', function () {
    $role = new Role(['name' => 'Test', 'guard_name' => 'api']);

    $roleData = RoleData::fromModel($role);

    expect($roleData->name)->toBe('Test');
    expect($roleData->guard_name)->toBe('api');
});

test('role data to array', function () {
    $roleData = new RoleData('Admin', 'web');

    $array = $roleData->toArray();

    expect($array)->toBe([
        'name' => 'Admin',
        'guard_name' => 'web',
    ]);
});
