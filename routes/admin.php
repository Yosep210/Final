<?php

use App\Livewire\AutoRo\Index as AutoRoIndex;
use App\Livewire\Bank\Index as BankIndex;
use App\Livewire\City\Index as CityIndex;
use App\Livewire\Commission\Index as CommissionIndex;
use App\Livewire\Commission\Statement as CommissionStatement;
use App\Livewire\Country\Index as CountryIndex;
use App\Livewire\District\Index as DistrictIndex;
use App\Livewire\Group\Index as GroupIndex;
use App\Livewire\Permission\Index as PermissionIndex;
use App\Livewire\Pin\AdminIndex as PinAdminIndex;
use App\Livewire\Pin\HistoryList as PinHistoryList;
use App\Livewire\Pin\MemberStock as PinMemberStock;
use App\Livewire\ProductOrder\Index as ProductOrderIndex;
use App\Livewire\Province\Index as ProvinceIndex;
use App\Livewire\Report\Budgeting;
use App\Livewire\Report\OmzetDaily;
use App\Livewire\Report\OmzetMonthly;
use App\Livewire\Report\OmzetOrder;
use App\Livewire\Report\Pairing;
use App\Livewire\Report\Registration;
use App\Livewire\Report\Reward;
use App\Livewire\Report\Ro;
use App\Livewire\Report\Tax;
use App\Livewire\Role\Index as RoleIndex;
use App\Livewire\Sponsor\Index as SponsorIndex;
use App\Livewire\Village\Index as VillageIndex;
use App\Livewire\Wallet\Index as WalletIndex;
use App\Livewire\Withdraw\Index as WithdrawIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:Admin|Staff'])->group(function () {

    Route::livewire('pin', PinAdminIndex::class)
        ->middleware('can:access-pin-generate')
        ->name('pin.index');

    Route::livewire('pin/stock', PinMemberStock::class)
        ->middleware('can:access-pin-stock')
        ->name('pin.stock.index');

    Route::livewire('pin/history', PinHistoryList::class)
        ->middleware('can:access-pin-transfer')
        ->name('pin.history.index');

    Route::livewire('city', CityIndex::class)
        ->middleware('can:access-setting-general')
        ->name('city.index');

    Route::livewire('country', CountryIndex::class)
        ->middleware('can:access-setting-general')
        ->name('country.index');

    Route::livewire('bank', BankIndex::class)
        ->middleware('can:access-setting-general')
        ->name('bank.index');

    Route::livewire('district', DistrictIndex::class)
        ->middleware('can:access-setting-general')
        ->name('district.index');

    Route::livewire('permission', PermissionIndex::class)
        ->middleware('can:access-setting-staff')
        ->name('permission.index');

    Route::livewire('province', ProvinceIndex::class)
        ->middleware('can:access-setting-general')
        ->name('province.index');

    Route::livewire('role', RoleIndex::class)
        ->middleware('can:access-setting-staff')
        ->name('role.index');

    Route::livewire('village', VillageIndex::class)
        ->middleware('can:access-setting-general')
        ->name('village.index');

    Route::livewire('sponsor', SponsorIndex::class)
        ->middleware('can:access-member-sponsor')
        ->name('sponsor.index');

    Route::livewire('group', GroupIndex::class)
        ->middleware('can:access-member-list')
        ->name('group.index');

    Route::livewire('commission', CommissionIndex::class)
        ->middleware('can:access-finance-bonus')
        ->name('commission.index');

    Route::livewire('commission/statement', CommissionStatement::class)
        ->middleware('can:access-finance-statement')
        ->name('commission.statement');

    Route::livewire('wallet', WalletIndex::class)
        ->middleware('can:access-finance-ewallet')
        ->name('wallet.index');

    Route::livewire('auto-ro', AutoRoIndex::class)
        ->middleware('can:access-finance-autoro')
        ->name('auto.ro.index');

    Route::livewire('withdraw', WithdrawIndex::class)
        ->middleware('can:access-finance-withdraw')
        ->name('withdraw.index');

    Route::livewire('product-order', ProductOrderIndex::class)
        ->middleware('can:access-pin-order')
        ->name('product.order.index');

    // Laporan (Report) Routes
    Route::livewire('report/registration', Registration::class)
        ->middleware('can:access-report-registration')
        ->name('report.registration');

    Route::livewire('report/ro', Ro::class)
        ->middleware('can:access-report-ro')
        ->name('report.ro');

    Route::livewire('report/pairing', Pairing::class)
        ->middleware('can:access-report-pairing')
        ->name('report.pairing');

    Route::livewire('report/omzet-daily', OmzetDaily::class)
        ->middleware('can:access-report-omzet-posting-daily')
        ->name('report.omzet-daily');

    Route::livewire('report/omzet-monthly', OmzetMonthly::class)
        ->middleware('can:access-report-omzet-posting-monthly')
        ->name('report.omzet-monthly');

    Route::livewire('report/omzet-order', OmzetOrder::class)
        ->middleware('can:access-report-omzet-order')
        ->name('report.omzet-order');

    Route::livewire('report/budgeting', Budgeting::class)
        ->middleware('can:access-report-budgeting')
        ->name('report.budgeting');

    Route::livewire('report/tax', Tax::class)
        ->middleware('can:access-report-tax')
        ->name('report.tax');

    Route::livewire('report/reward', Reward::class)
        ->middleware('can:access-report-reward')
        ->name('report.reward');
});
