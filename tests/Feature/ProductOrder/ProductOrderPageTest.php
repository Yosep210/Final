<?php

use App\Livewire\ProductOrder\Index as ProductOrderIndex;
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
