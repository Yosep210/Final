<?php

use App\Livewire\Pin\HistoryList as PinHistoryList;
use App\Livewire\Pin\MemberStock as PinMemberStock;
use App\Models\Member;
use App\Models\Pin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('Admin', 'web');
});

it('renders the kirim produk page for authenticated admin members', function () {
    $member = Member::factory()->active()->create();
    $member->assignRole('Admin');

    $this->actingAs($member)
        ->get(route('pin.index'))
        ->assertOk()
        ->assertSee('Activation PINs');
});

it('renders the stock produk member page for authenticated admin members', function () {
    $member = Member::factory()->active()->create();
    $member->assignRole('Admin');

    $this->actingAs($member)
        ->get(route('pin.stock.index'))
        ->assertOk()
        ->assertSee('Stock Produk Member');
});

it('renders the riwayat pin page for authenticated admin members', function () {
    $member = Member::factory()->active()->create();
    $member->assignRole('Admin');

    $this->actingAs($member)
        ->get(route('pin.history.index'))
        ->assertOk()
        ->assertSee('Riwayat PIN');
});

it('restricts PIN pages for guests and regular members', function () {
    $routes = [
        route('pin.index'),
        route('pin.stock.index'),
        route('pin.history.index'),
    ];

    foreach ($routes as $route) {
        // Guest
        $response = $this->get($route);
        $this->assertTrue($response->isRedirect(route('login')) || $response->status() === 403);

        // Regular member
        $member = Member::factory()->active()->create();
        $this->actingAs($member)
            ->get($route)
            ->assertForbidden();
    }
});

it('verifies member stock counts correctly', function () {
    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    $member = Member::factory()->active()->create();
    Pin::query()->create([
        'serial_number' => 'SN001',
        'pin_code' => 'PIN001',
        'status' => 'unused',
        'owner_id' => $member->id,
    ]);
    Pin::query()->create([
        'serial_number' => 'SN002',
        'pin_code' => 'PIN002',
        'status' => 'used',
        'owner_id' => $member->id,
    ]);

    Livewire::actingAs($admin)
        ->test(PinMemberStock::class)
        ->assertSee(strtoupper($member->username))
        ->assertSee('2') // Total pins
        ->assertSee('1'); // Active and Used pins
});

it('verifies history list displays PIN records correctly', function () {
    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    $member = Member::factory()->active()->create();
    $pin = Pin::query()->create([
        'serial_number' => 'SN-HIST-001',
        'pin_code' => 'PIN-HIST-001',
        'status' => 'unused',
        'owner_id' => $member->id,
    ]);

    Livewire::actingAs($admin)
        ->test(PinHistoryList::class)
        ->assertSee('SN-HIST-001')
        ->set('searchSerial', 'SN-HIST-999')
        ->assertDontSee('SN-HIST-001');
});
