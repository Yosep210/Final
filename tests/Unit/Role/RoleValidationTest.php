<?php

use App\Http\Requests\Role\StoreRoleRequest;
use App\Models\Role;
use Illuminate\Validation\Rules\Unique;

it('builds create role rules without ignored model', function () {
    $rules = StoreRoleRequest::roleRules();
    $uniqueRule = collect($rules['name'])->first(fn (mixed $rule) => $rule instanceof Unique);

    expect($uniqueRule)->toBeInstanceOf(Unique::class)
        ->and((string) $uniqueRule)->toContain('unique:roles,name');
});

it('builds update role rules with ignored model', function () {
    $role = new Role;
    $role->id = 42;

    $rules = StoreRoleRequest::roleRules($role);
    $uniqueRule = collect($rules['name'])->first(fn (mixed $rule) => $rule instanceof Unique);

    expect($uniqueRule)->toBeInstanceOf(Unique::class)
        ->and((string) $uniqueRule)->toContain('unique:roles,name');
});
