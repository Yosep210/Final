<?php

use App\Livewire\Member\Index as MemberIndex;
use App\Models\Member;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('about', 'about')->name('about');
Route::view('contact', 'contact')->name('contact');
Route::view('edukasi', 'edukasi')->name('edukasi');
Route::view('product', 'product')->name('product');
Route::view('opportunity', 'opportunity')->name('opportunity');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('member', MemberIndex::class)
        ->middleware('can:viewAny,'.Member::class)
        ->name('member.index');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
