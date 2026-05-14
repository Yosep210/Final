<?php

use App\Http\Controllers\CountryController;
use App\Http\Controllers\MemberController;
use App\Livewire\Country\Index as CountryIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('country', CountryIndex::class)->name('country.index');
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('members', MemberController::class);
});

require __DIR__.'/settings.php';
