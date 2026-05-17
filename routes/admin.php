<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\RoleController;
use App\Livewire\Country\Index as CountryIndex;
use App\Livewire\Member\Index as MemberIndex;
use App\Livewire\Role\Index as RoleIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('country', CountryIndex::class)->name('country.index');
    Route::apiResource('countries', CountryController::class);

    Route::livewire('member', MemberIndex::class)->name('member.index');
    Route::apiResource('members', MemberController::class);

    Route::livewire('role', RoleIndex::class)->name('role.index');
    Route::apiResource('roles', RoleController::class);
});
