<?php

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\RoleController;
use App\Livewire\City\Index as CityIndex;
use App\Livewire\Country\Index as CountryIndex;
use App\Livewire\District\Index as DistrictIndex;
use App\Livewire\Member\Index as MemberIndex;
use App\Livewire\Permission\Index as PermissionIndex;
use App\Livewire\Province\Index as ProvinceIndex;
use App\Livewire\Role\Index as RoleIndex;
use App\Livewire\RolePermission\Index as RolePermissionIndex;
use App\Livewire\Village\Index as VillageIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('country', CountryIndex::class)->name('country.index');

    Route::livewire('district', DistrictIndex::class)->name('district.index');

    Route::livewire('province', ProvinceIndex::class)->name('province.index');

    Route::livewire('city', CityIndex::class)->name('city.index');

    Route::livewire('village', VillageIndex::class)->name('village.index');

    Route::livewire('member', MemberIndex::class)->name('member.index');
    Route::apiResource('members', MemberController::class);

    Route::livewire('role', RoleIndex::class)->name('role.index');
    Route::apiResource('roles', RoleController::class);

    Route::livewire('permission', PermissionIndex::class)->name('permission.index');
    Route::apiResource('permissions', PermissionController::class);

    Route::livewire('role-permission', RolePermissionIndex::class)->name('role-permission.index');
});
