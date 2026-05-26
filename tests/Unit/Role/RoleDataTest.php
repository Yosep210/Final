<?php

use App\Data\RoleData;
use Spatie\Permission\Models\Role;

it('normalizes role data from array input', function () {
    $data = RoleData::fromArray([
        'name' => '  Admin  ',
        'guard_name' => '  web  ',
    ]);

    expect($data->name)->toBe('Admin')
        ->and($data->guardName)->toBe('web');
});

it('creates role data from model', function () {
    $role = new Role(['name' => 'Manager', 'guard_name' => 'api']);
    $data = RoleData::fromModel($role);

    expect($data->name)->toBe('Manager')
        ->and($data->guardName)->toBe('api');
});
