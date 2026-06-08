<?php

use App\Livewire\Dashboard;
use App\Livewire\Member\Index as MemberIndex;
use App\Livewire\Pin\MemberIndex as PinMemberIndex;
use App\Livewire\Wallet\MemberWallet;
use App\Models\Member;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('about', 'about')->name('about');
Route::view('contact', 'contact')->name('contact');
Route::view('edukasi', 'edukasi')->name('edukasi');
Route::view('product', 'product')->name('product');
Route::view('opportunity', 'opportunity')->name('opportunity');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('my-pins', PinMemberIndex::class)->name('my.pin.index');
    Route::livewire('my-wallet', MemberWallet::class)->name('my.wallet.index');

    Route::livewire('member', MemberIndex::class)
        ->middleware('can:viewAny,'.Member::class)
        ->name('member.index');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
