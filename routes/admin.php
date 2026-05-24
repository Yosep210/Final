<?php

use App\Livewire\City\Index as CityIndex;
use App\Livewire\Country\Index as CountryIndex;
use App\Livewire\District\Index as DistrictIndex;
use App\Livewire\Permission\Index as PermissionIndex;
use App\Livewire\Province\Index as ProvinceIndex;
use App\Livewire\Role\Index as RoleIndex;
use App\Livewire\Village\Index as VillageIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:Admin'])->group(function () {

    Route::livewire('city', CityIndex::class)->name('city.index');

    Route::livewire('country', CountryIndex::class)->name('country.index');

    Route::livewire('district', DistrictIndex::class)->name('district.index');

    Route::livewire('permission', PermissionIndex::class)->name('permission.index');

    Route::livewire('province', ProvinceIndex::class)->name('province.index');

    Route::livewire('role', RoleIndex::class)->name('role.index');

    Route::livewire('village', VillageIndex::class)->name('village.index');
});
