<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\MemberController;
use App\Livewire\Country\Index as CountryIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('country', CountryIndex::class)->name('country.index');
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('members', MemberController::class);
});
