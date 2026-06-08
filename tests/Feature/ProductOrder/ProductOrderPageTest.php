<?php

use App\Livewire\ProductOrder\Index as ProductOrderIndex;
use App\Models\EwalletLog;
use App\Models\Member;
use App\Models\ProductOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('Admin', 'web');
});

it('renders the product order management page for authenticated admin members', function () {
    $member = Member::factory()->active()->create();
    $member->assignRole('Admin');

    $this->actingAs($member)
        ->get(route('product.order.index'))
        ->assertOk()
        ->assertSee('Product Order');
});

it('restricts product order management page for guests and regular members', function () {
    // Guest
    $this->get(route('product.order.index'))
        ->assertRedirect(route('login'));

    // Regular member
    $member = Member::factory()->active()->create();
    $this->actingAs($member)
        ->get(route('product.order.index'))
        ->assertForbidden();
});

it('can display order details in modal via livewire event', function () {
    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    $member = Member::factory()->active()->create();
    $order = ProductOrder::query()->create([
        'member_id' => $member->id,
        'invoice' => 'INV/TEST/001',
        'status' => 1,
        'total_bv' => 100,
        'total_checkout' => 150000,
        'payment_method' => 'Bank Transfer',
        'shipping_method' => 'Courier Service',
        'shipping_courier' => 'jne',
        'shipping_service' => 'REG',
        'shipping_address' => "Main Street\nNo. 123",
    ]);

    Livewire::actingAs($admin)
        ->test(ProductOrderIndex::class)
        ->assertSet('showModal', false)
        ->assertSet('selectedOrder', null)
        ->dispatch('product-order:view-detail', orderId: $order->id)
        ->assertSet('showModal', true)
        ->assertSet('selectedOrder.id', $order->id)
        ->call('closeModal')
        ->assertSet('showModal', false)
        ->assertSet('selectedOrder', null);
});

it('allows admin to confirm a review order', function () {
    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    $member = Member::factory()->active()->create();
    $order = ProductOrder::query()->create([
        'member_id' => $member->id,
        'invoice' => 'INV/TEST/CONFIRM',
        'status' => 0, // Review
        'total_checkout' => 150000.00,
        'total_payment' => 0.00,
        'payment_method' => 'transfer',
    ]);

    Livewire::actingAs($admin)
        ->test(ProductOrderIndex::class)
        ->call('confirmOrder', $order->id)
        ->assertDispatched('toast', variant: 'success', content: 'Pesanan berhasil dikonfirmasi.');

    expect($order->fresh()->status)->toBe(1)
        ->and((float) $order->fresh()->total_payment)->toBe(150000.00);
});

it('allows admin to mark a confirmed order as done', function () {
    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    $member = Member::factory()->active()->create();
    $order = ProductOrder::query()->create([
        'member_id' => $member->id,
        'invoice' => 'INV/TEST/DONE',
        'status' => 1, // Confirmed
        'total_checkout' => 150000.00,
        'total_payment' => 150000.00,
        'payment_method' => 'wallet',
    ]);

    Livewire::actingAs($admin)
        ->test(ProductOrderIndex::class)
        ->call('doneOrder', $order->id)
        ->assertDispatched('toast', variant: 'success', content: 'Pesanan ditandai selesai.');

    expect($order->fresh()->status)->toBe(2);
});

it('allows admin to cancel an order and refunds e-wallet balance if paid by wallet', function () {
    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    $member = Member::factory()->active()->create();

    // Give member initial wallet balance of 200,000 via IN log, then deduct 150,000 via order payment OUT log
    EwalletLog::create([
        'member_id' => $member->id,
        'type' => 'IN',
        'nominal' => 200000.00,
        'amount' => 200000.00,
    ]);

    $order = ProductOrder::query()->create([
        'member_id' => $member->id,
        'invoice' => 'INV/TEST/CANCEL',
        'status' => 1, // Confirmed
        'total_checkout' => 150000.00,
        'total_payment' => 150000.00,
        'payment_method' => 'wallet',
    ]);

    EwalletLog::create([
        'member_id' => $member->id,
        'source_id' => $order->id,
        'source' => 'order_payment',
        'type' => 'OUT',
        'nominal' => 150000.00,
        'amount' => 150000.00,
        'status' => 1,
    ]);

    expect($member->ewalletBalance())->toBe(50000.00);

    Livewire::actingAs($admin)
        ->test(ProductOrderIndex::class)
        ->call('cancelOrder', $order->id)
        ->assertDispatched('toast', variant: 'success');

    expect($order->fresh()->status)->toBe(4); // Cancelled

    // Wallet balance should be refunded: 50,000 + 150,000 = 200,000
    expect($member->ewalletBalance())->toBe(200000.00);

    // Verify refund log was created
    $this->assertDatabaseHas('ewallet_logs', [
        'member_id' => $member->id,
        'source_id' => $order->id,
        'source' => 'order_refund',
        'type' => 'IN',
        'nominal' => 150000.00,
        'amount' => 150000.00,
    ]);
});
