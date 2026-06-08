<?php

use App\Livewire\Shop\Checkout;
use App\Livewire\Shop\MemberOrders;
use App\Livewire\Shop\ProductList;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\EwalletLog;
use App\Models\Member;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductStockist;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup Country and Location Data
    $this->country = Country::query()->create([
        'iso' => 'ID',
        'name' => 'Indonesia',
        'nice_name' => 'Indonesia',
        'iso3' => 'IDN',
        'numcode' => 360,
        'phonecode' => 62,
        'status' => true,
    ]);

    $this->province = Province::create([
        'country_id' => $this->country->id,
        'name' => 'Jawa Barat',
    ]);

    $this->city = City::create([
        'province_id' => $this->province->id,
        'name' => 'Bandung',
        'type' => 'kota',
    ]);

    $this->district = District::create([
        'city_id' => $this->city->id,
        'name' => 'Coblong',
    ]);

    $this->village = Village::create([
        'district_id' => $this->district->id,
        'name' => 'Dago',
        'postal_code' => '40135',
    ]);

    // Setup Products
    $this->product1 = Product::create([
        'sku' => 'PROD-001',
        'name' => 'Paket Herbal A',
        'slug' => 'paket-herbal-a',
        'price' => 100000.00, // Stockist price
        'price_member' => 120000.00, // Regular member price
        'weight' => 500,
        'bv' => 100,
        'reward_point' => 1.5,
        'status' => true,
        'show_order' => true,
        'varian' => 'Original, Madu',
    ]);

    $this->product2 = Product::create([
        'sku' => 'PROD-002',
        'name' => 'Paket Kecantikan B',
        'slug' => 'paket-kecantikan-b',
        'price' => 200000.00,
        'price_member' => 250000.00,
        'weight' => 1000,
        'bv' => 200,
        'reward_point' => 3.0,
        'status' => true,
        'show_order' => true,
    ]);

    // Default configuration for stockist/shipping
    config([
        'mlm.stockist.minimum_order' => [
            1 => 7500000.00, // Mobile Stockist
        ],
        'mlm.stockist.discount' => [
            1 => 5.0, // 5%
        ],
        'mlm.shipping.rajaongkir_origin' => 151,
        'mlm.shipping.rajaongkir_token' => 'test-token',
        'mlm.shipping.rajaongkir_url' => 'https://rajaongkir.komerce.id/api/v1/',
    ]);
});

it('restricts shop pages for guest users', function () {
    $this->get(route('shop.index'))->assertRedirect(route('login'));
    $this->get(route('shop.checkout'))->assertRedirect(route('login'));
    $this->get(route('shop.orders'))->assertRedirect(route('login'));
});

it('allows regular members to view products list and add products to cart', function () {
    $member = Member::factory()->active()->create(['type' => 0]);

    $this->actingAs($member);

    // Initial state: empty cart session
    expect(session()->get('shop_cart'))->toBeNull();

    Livewire::test(ProductList::class)
        ->assertSee('Toko Online JPBuana')
        ->assertSee('Paket Herbal A')
        ->set('selectedVariant', 'Madu')
        ->set('quantity', 2)
        ->call('addToCart', $this->product1->id)
        ->assertDispatched('toast', variant: 'success')
        ->assertSet('cart', function ($cart) {
            $key = $this->product1->id.'_Madu';

            return isset($cart[$key]) &&
                $cart[$key]['product_id'] === $this->product1->id &&
                $cart[$key]['qty'] === 2 &&
                (float) $cart[$key]['price'] === 120000.00; // Regular member price
        });

    // Check cart is persisted to session
    $sessionCart = session()->get('shop_cart');
    expect($sessionCart)->not->toBeEmpty();
    expect(count($sessionCart))->toBe(1);
});

it('allows stockists to add to cart at stockist prices', function () {
    $stockist = Member::factory()->active()->create(['type' => 1]); // Mobile Stockist

    Livewire::actingAs($stockist)
        ->test(ProductList::class)
        ->call('addToCart', $this->product2->id)
        ->assertSet('cart', function ($cart) {
            return isset($cart[$this->product2->id]) &&
                $cart[$this->product2->id]['price'] === 200000.00; // Stockist price
        });
});

it('can manage cart quantities and remove items', function () {
    $member = Member::factory()->active()->create(['type' => 0]);
    $cartKey = $this->product2->id;

    // Seed cart in session
    session()->put('shop_cart', [
        $cartKey => [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'qty' => 1,
            'price' => 250000.00,
            'weight' => 1000,
            'bv' => 200,
            'point' => 3.0,
            'image' => null,
            'variant' => '',
        ],
    ]);

    Livewire::actingAs($member)
        ->test(ProductList::class)
        ->assertSet('cart.'.$cartKey.'.qty', 1)
        // Increase qty
        ->call('updateQty', $cartKey, 3)
        ->assertSet('cart.'.$cartKey.'.qty', 3)
        // Decrease to 0 (should remove)
        ->call('updateQty', $cartKey, 0)
        ->assertSet('cart', []);
});

it('redirects to shop if checkout page accessed with empty cart', function () {
    $member = Member::factory()->active()->create(['type' => 0]);

    Livewire::actingAs($member)
        ->test(Checkout::class)
        ->assertRedirect(route('shop.index'));
});

it('validates stockist minimum checkout requirement', function () {
    $stockist = Member::factory()->active()->create(['type' => 1]); // Mobile stockist, min order 7.5m

    // Put a small order in cart (300k)
    session()->put('shop_cart', [
        $this->product2->id => [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'qty' => 1,
            'price' => 200000.00,
            'weight' => 1000,
            'bv' => 200,
            'point' => 3.0,
            'image' => null,
            'variant' => '',
        ],
    ]);

    Livewire::actingAs($stockist)
        ->test(Checkout::class)
        ->set('shippingMethod', 'pickup')
        ->set('paymentMethod', 'transfer')
        ->call('placeOrder')
        ->assertDispatched('toast', variant: 'danger', heading: 'Minimum Belanja');

    // Add more to pass minimum order (40 items * 200k = 8m)
    session()->put('shop_cart', [
        $this->product2->id => [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'qty' => 40,
            'price' => 200000.00,
            'weight' => 1000,
            'bv' => 200,
            'point' => 3.0,
            'image' => null,
            'variant' => '',
        ],
    ]);

    Livewire::actingAs($stockist)
        ->test(Checkout::class)
        ->set('shippingMethod', 'pickup')
        ->set('paymentMethod', 'transfer')
        ->call('placeOrder')
        // It shouldn't trigger minimum order toast
        ->assertNotDispatched('toast', heading: 'Minimum Belanja');
});

it('validates wallet balance and password for e-wallet payments', function () {
    $member = Member::factory()->active()->create([
        'type' => 0,
        'password' => Hash::make('mypassword123'),
    ]);

    session()->put('shop_cart', [
        $this->product2->id => [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'qty' => 1,
            'price' => 250000.00,
            'weight' => 1000,
            'bv' => 200,
            'point' => 3.0,
            'image' => null,
            'variant' => '',
        ],
    ]);

    // Test 1: Insufficient balance
    Livewire::actingAs($member)
        ->test(Checkout::class)
        ->set('shippingMethod', 'pickup')
        ->set('paymentMethod', 'wallet')
        ->set('passwordConfirm', 'mypassword123')
        ->call('placeOrder')
        ->assertDispatched('toast', variant: 'danger', heading: 'Saldo Kurang');

    // Give balance (300,000)
    EwalletLog::create([
        'member_id' => $member->id,
        'nominal' => 300000.00,
        'amount' => 300000.00,
        'type' => 'IN',
    ]);

    // Test 2: Correct balance but wrong password
    Livewire::actingAs($member)
        ->test(Checkout::class)
        ->set('shippingMethod', 'pickup')
        ->set('paymentMethod', 'wallet')
        ->set('passwordConfirm', 'wrong-pass')
        ->call('placeOrder')
        ->assertDispatched('toast', variant: 'danger', heading: 'Password Salah');
});

it('successfully checks out with e-wallet, deducts wallet, and redirects to orders', function () {
    $member = Member::factory()->active()->create([
        'type' => 0,
        'password' => Hash::make('my-secure-password'),
    ]);

    // Give balance (300,000)
    EwalletLog::create([
        'member_id' => $member->id,
        'nominal' => 300000.00,
        'amount' => 300000.00,
        'type' => 'IN',
    ]);

    session()->put('shop_cart', [
        $this->product2->id => [
            'product_id' => $this->product2->id,
            'name' => $this->product2->name,
            'qty' => 1,
            'price' => 250000.00,
            'weight' => 1000,
            'bv' => 200,
            'point' => 3.0,
            'image' => null,
            'variant' => '',
        ],
    ]);

    Livewire::actingAs($member)
        ->test(Checkout::class)
        ->set('shippingMethod', 'pickup')
        ->set('paymentMethod', 'wallet')
        ->set('passwordConfirm', 'my-secure-password')
        ->call('placeOrder')
        ->assertRedirect(route('shop.orders'));

    // Check cart is cleared
    expect(session()->get('shop_cart'))->toBeNull();

    // Check order created in database
    $this->assertDatabaseHas('product_orders', [
        'member_id' => $member->id,
        'status' => 1, // Auto-confirmed because paid by wallet
        'payment_method' => 'wallet',
        'subtotal' => 250000.00,
        'total_checkout' => 250000.00,
        'total_payment' => 250000.00,
        'type_order' => 'member',
    ]);

    $order = ProductOrder::where('member_id', $member->id)->first();

    $this->assertDatabaseHas('product_order_details', [
        'product_order_id' => $order->id,
        'product_id' => $this->product2->id,
        'price' => 250000.00,
        'qty' => 1,
        'subtotal' => 250000.00,
    ]);

    // Check Ewallet deduction OUT log
    $this->assertDatabaseHas('ewallet_logs', [
        'member_id' => $member->id,
        'source_id' => $order->id,
        'source' => 'order_payment',
        'type' => 'OUT',
        'nominal' => 250000.00,
        'amount' => 250000.00,
        'status' => 1,
    ]);

    // Wallet balance should be reduced: 300,000 - 250,000 = 50,000
    expect($member->ewalletBalance())->toBe(50000.00);
});

it('successfully checks out with bank transfer requiring manual review', function () {
    $member = Member::factory()->active()->create(['type' => 0]);

    session()->put('shop_cart', [
        $this->product1->id.'_Original' => [
            'product_id' => $this->product1->id,
            'name' => $this->product1->name,
            'qty' => 2,
            'price' => 120000.00,
            'weight' => 500,
            'bv' => 100,
            'point' => 1.5,
            'image' => null,
            'variant' => 'Original',
        ],
    ]);

    Livewire::actingAs($member)
        ->test(Checkout::class)
        ->set('shippingMethod', 'pickup')
        ->set('paymentMethod', 'transfer')
        ->call('placeOrder')
        ->assertRedirect(route('shop.orders'));

    // Check order status is 0 (Review)
    $this->assertDatabaseHas('product_orders', [
        'member_id' => $member->id,
        'status' => 0, // Awaiting review
        'payment_method' => 'transfer',
        'subtotal' => 240000.00,
        'total_checkout' => 240000.00,
        'total_payment' => 0.00,
    ]);

    // Ewallet balance should not be affected
    expect($member->ewalletBalance())->toBe(0.00);
});

it('lists and filters orders on the member orders page', function () {
    $member = Member::factory()->active()->create(['type' => 0]);

    // Create a review order
    $order1 = ProductOrder::create([
        'invoice' => 'INV/REVIEW/01',
        'member_id' => $member->id,
        'status' => 0, // Review
        'total_checkout' => 100000.00,
        'payment_method' => 'transfer',
        'shipping_method' => 'pickup',
    ]);

    // Create a confirmed order
    $order2 = ProductOrder::create([
        'invoice' => 'INV/CONFIRMED/02',
        'member_id' => $member->id,
        'status' => 1, // Confirmed
        'total_checkout' => 200000.00,
        'payment_method' => 'wallet',
        'shipping_method' => 'pickup',
    ]);

    Livewire::actingAs($member)
        ->test(MemberOrders::class)
        // See both orders
        ->assertSee('INV/REVIEW/01')
        ->assertSee('INV/CONFIRMED/02')
        // Search filter
        ->set('search', 'INV/REVIEW')
        ->assertSee('INV/REVIEW/01')
        ->assertDontSee('INV/CONFIRMED/02')
        ->set('search', '')
        // Status filter
        ->set('statusFilter', '1') // Confirmed
        ->assertSee('INV/CONFIRMED/02')
        ->assertDontSee('INV/REVIEW/01')
        // Detail view modal trigger
        ->assertSet('selectedOrderId', null)
        ->call('viewDetails', $order2->id)
        ->assertSet('selectedOrderId', $order2->id)
        ->assertSee('Detail Invoice INV/CONFIRMED/02')
        ->call('closeModal')
        ->assertSet('selectedOrderId', null);
});

it('handles stockist stock additions when stockist buys from Pusat', function () {
    $stockist = Member::factory()->active()->create(['type' => 1]); // Mobile stockist

    // Create a product order for the stockist purchasing from Pusat
    $order = ProductOrder::create([
        'invoice' => 'INV/STK/IN',
        'member_id' => $stockist->id,
        'stockist_id' => 0, // From Pusat
        'type_order' => 'stockist',
        'status' => 0, // Review
        'products_json' => json_encode([
            [
                'product_id' => $this->product1->id,
                'variant' => 'Original',
                'qty' => 10,
                'price' => 100000.00,
            ],
        ]),
    ]);

    // Initial stock should be 0
    expect(ProductStockist::getStock($stockist->id, $this->product1->id, 'Original'))->toBe(0);

    // Confirming order should add stock
    $order->update(['status' => 1]); // Confirmed

    expect(ProductStockist::getStock($stockist->id, $this->product1->id, 'Original'))->toBe(10);

    // Verify database entries
    $this->assertDatabaseHas('product_stockist', [
        'id_member' => $stockist->id,
        'id_source' => $order->id,
        'product' => $this->product1->id,
        'varian' => 'Original',
        'qty' => 10,
        'type' => 'IN',
    ]);
});

it('handles stockist stock deductions when member buys from a stockist', function () {
    $stockist = Member::factory()->active()->create(['type' => 1]);
    $member = Member::factory()->active()->create(['type' => 0]);

    // Seed initial stockist stock: 25 items
    ProductStockist::create([
        'id_member' => $stockist->id,
        'product' => $this->product1->id,
        'varian' => 'Madu',
        'qty' => 25,
        'type' => 'IN',
        'status' => 1,
    ]);

    expect(ProductStockist::getStock($stockist->id, $this->product1->id, 'Madu'))->toBe(25);

    // Create a member order served by this stockist
    $order = ProductOrder::create([
        'invoice' => 'INV/MEM/OUT',
        'member_id' => $member->id,
        'stockist_id' => $stockist->id, // Served by stockist
        'type_order' => 'member',
        'status' => 0, // Review
        'products_json' => json_encode([
            [
                'product_id' => $this->product1->id,
                'variant' => 'Madu',
                'qty' => 5,
                'price' => 120000.00,
            ],
        ]),
    ]);

    // Confirming member order should deduct stockist stock: 25 - 5 = 20
    $order->update(['status' => 1]);

    expect(ProductStockist::getStock($stockist->id, $this->product1->id, 'Madu'))->toBe(20);

    // Verify database entry
    $this->assertDatabaseHas('product_stockist', [
        'id_member' => $stockist->id,
        'id_source' => $order->id,
        'product' => $this->product1->id,
        'varian' => 'Madu',
        'qty' => 5,
        'type' => 'OUT',
    ]);
});

it('reverts stockist stock when member order is cancelled', function () {
    $stockist = Member::factory()->active()->create(['type' => 1]);
    $member = Member::factory()->active()->create(['type' => 0]);

    // Seed initial stockist stock: 10 items
    ProductStockist::create([
        'id_member' => $stockist->id,
        'product' => $this->product2->id,
        'varian' => '',
        'qty' => 10,
        'type' => 'IN',
        'status' => 1,
    ]);

    // Member buys 3 items
    $order = ProductOrder::create([
        'invoice' => 'INV/MEM/CANCELLED',
        'member_id' => $member->id,
        'stockist_id' => $stockist->id,
        'type_order' => 'member',
        'status' => 1, // Already Confirmed (so stock is already deducted)
        'products_json' => json_encode([
            [
                'product_id' => $this->product2->id,
                'variant' => '',
                'qty' => 3,
                'price' => 250000.00,
            ],
        ]),
    ]);

    // Verify initial state: 10 - 3 = 7 remaining
    expect(ProductStockist::getStock($stockist->id, $this->product2->id, ''))->toBe(7);

    // Cancel the order
    $order->update(['status' => 4]); // Cancelled

    // Stock should be reverted: 7 + 3 = 10
    expect(ProductStockist::getStock($stockist->id, $this->product2->id, ''))->toBe(10);

    // Verify refund database entry
    $this->assertDatabaseHas('product_stockist', [
        'id_member' => $stockist->id,
        'id_source' => $order->id,
        'product' => $this->product2->id,
        'varian' => '',
        'qty' => 3,
        'type' => 'IN',
        'description' => 'Refund Stok dari Pembatalan Pesanan #INV/MEM/CANCELLED',
    ]);
});
