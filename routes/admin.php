<?php

use App\Livewire\AutoRo\Index as AutoRoIndex;
use App\Livewire\City\Index as CityIndex;
use App\Livewire\Commission\Index as CommissionIndex;
use App\Livewire\Commission\Statement as CommissionStatement;
use App\Livewire\Country\Index as CountryIndex;
use App\Livewire\District\Index as DistrictIndex;
use App\Livewire\Generation\Index as GenerationIndex;
use App\Livewire\Group\Index as GroupIndex;
use App\Livewire\Network\Index as NetworkIndex;
use App\Livewire\Permission\Index as PermissionIndex;
use App\Livewire\Pin\AdminIndex as PinAdminIndex;
use App\Livewire\Province\Index as ProvinceIndex;
use App\Livewire\Role\Index as RoleIndex;
use App\Livewire\Sponsor\Index as SponsorIndex;
use App\Livewire\Village\Index as VillageIndex;
use App\Livewire\Wallet\Index as WalletIndex;
use App\Livewire\Withdraw\Index as WithdrawIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:Admin'])->group(function () {

    Route::livewire('pin', PinAdminIndex::class)->name('pin.index');

    Route::livewire('city', CityIndex::class)->name('city.index');

    Route::livewire('country', CountryIndex::class)->name('country.index');

    Route::livewire('district', DistrictIndex::class)->name('district.index');

    Route::livewire('permission', PermissionIndex::class)->name('permission.index');

    Route::livewire('province', ProvinceIndex::class)->name('province.index');

    Route::livewire('role', RoleIndex::class)->name('role.index');

    Route::livewire('village', VillageIndex::class)->name('village.index');

    Route::livewire('sponsor', SponsorIndex::class)->name('sponsor.index');

    Route::livewire('group', GroupIndex::class)->name('group.index');

    Route::livewire('generation', GenerationIndex::class)->name('generation.index');

    Route::livewire('network/{username?}', NetworkIndex::class)->name('network.index');

    Route::livewire('commission', CommissionIndex::class)->name('commission.index');
    Route::livewire('commission/statement', CommissionStatement::class)->name('commission.statement');
    Route::livewire('wallet', WalletIndex::class)->name('wallet.index');
    Route::livewire('auto-ro', AutoRoIndex::class)->name('auto.ro.index');
    Route::livewire('withdraw', WithdrawIndex::class)->name('withdraw.index');
});
