<?php

namespace App\Livewire\Shop;

use App\Models\City;
use App\Models\District;
use App\Models\EwalletLog;
use App\Models\Member;
use App\Models\ProductOrder;
use App\Models\ProductOrderDetail;
use App\Models\Province;
use App\Models\Village;
use App\Services\ProductOrderService;
use App\Services\RajaongkirService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class Checkout extends Component
{
    // Cart contents
    public array $cart = [];

    // Form fields
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public ?int $provinceId = null;

    public ?int $cityId = null;

    public ?int $districtId = null;

    public ?int $villageId = null;

    public string $address = '';

    public string $shippingMethod = 'pickup'; // pickup, ekspedisi

    public string $productOrderTo = 'pusat'; // pusat, stockist

    public ?int $selectStockistId = null;

    public string $selectedCourier = 'jne'; // jne, jnt, pos

    public string $selectedService = '';

    public float $courierCost = 0.0;

    public string $selectedServiceDescription = '';

    public string $paymentMethod = 'transfer'; // transfer, wallet

    public string $passwordConfirm = '';

    // Cached lists for cascading dropdowns
    public array $cities = [];

    public array $districts = [];

    public array $villages = [];

    public array $courierServices = [];

    protected $rules = [
        'name' => 'required_if:shippingMethod,ekspedisi|string|max:150',
        'phone' => 'required_if:shippingMethod,ekspedisi|string|max:20',
        'email' => 'required_if:shippingMethod,ekspedisi|email|max:150',
        'provinceId' => 'required_if:shippingMethod,ekspedisi',
        'cityId' => 'required_if:shippingMethod,ekspedisi',
        'districtId' => 'required_if:shippingMethod,ekspedisi',
        'villageId' => 'required_if:shippingMethod,ekspedisi',
        'address' => 'required_if:shippingMethod,ekspedisi|string',
        'paymentMethod' => 'required|in:transfer,wallet',
        'passwordConfirm' => 'required_if:paymentMethod,wallet',
        'selectStockistId' => 'required_if:productOrderTo,stockist',
    ];

    public function mount(ProductOrderService $orderService): void
    {
        $this->cart = session()->get('shop_cart', []);

        if (empty($this->cart)) {
            redirect()->route('shop.index');

            return;
        }

        $user = auth()->user();

        // Pre-fill user profile fields
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->profile?->phone ?? '';
        $this->address = $user->profile?->address ?? '';

        if ($user->profile?->province_id) {
            $this->provinceId = $user->profile->province_id;
            $this->updatedProvinceId($this->provinceId);
        }
        if ($user->profile?->city_id) {
            $this->cityId = $user->profile->city_id;
            $this->updatedCityId($this->cityId);
        }
        if ($user->profile?->district_id) {
            $this->districtId = $user->profile->district_id;
            $this->updatedDistrictId($this->districtId);
        }
        if ($user->profile?->village_id) {
            $this->villageId = $user->profile->village_id;
        }
    }

    /**
     * Handle cascading select updates.
     */
    public function updatedProvinceId(mixed $value): void
    {
        $this->cities = $value ? City::where('province_id', $value)->orderBy('name')->get()->toArray() : [];
        $this->cityId = null;
        $this->districts = [];
        $this->districtId = null;
        $this->villages = [];
        $this->villageId = null;
        $this->resetCourierCost();
    }

    public function updatedCityId(mixed $value): void
    {
        $this->districts = $value ? District::where('city_id', $value)->orderBy('name')->get()->toArray() : [];
        $this->districtId = null;
        $this->villages = [];
        $this->villageId = null;
        $this->calculateShipping();
    }

    public function updatedDistrictId(mixed $value): void
    {
        $this->villages = $value ? Village::where('district_id', $value)->orderBy('name')->get()->toArray() : [];
        $this->villageId = null;
    }

    public function updatedShippingMethod(): void
    {
        if ($this->shippingMethod === 'pickup') {
            $this->resetCourierCost();
        } else {
            $this->calculateShipping();
        }
    }

    public function updatedSelectedCourier(): void
    {
        $this->calculateShipping();
    }

    public function updatedSelectedService(string $value): void
    {
        if (empty($value)) {
            $this->courierCost = 0.0;
            $this->selectedServiceDescription = '';

            return;
        }

        foreach ($this->courierServices as $service) {
            if ($service['service'] === $value) {
                $this->courierCost = (float) $service['cost'][0]['value'];
                $this->selectedServiceDescription = $service['service'].' ('.$service['description'].' - ETD: '.$service['cost'][0]['etd'].')';
                break;
            }
        }
    }

    protected function resetCourierCost(): void
    {
        $this->courierCost = 0.0;
        $this->selectedService = '';
        $this->selectedServiceDescription = '';
        $this->courierServices = [];
    }

    /**
     * Query shipping rates via RajaongkirService.
     */
    public function calculateShipping(): void
    {
        if ($this->shippingMethod !== 'ekspedisi' || ! $this->cityId) {
            $this->resetCourierCost();

            return;
        }

        // Sum total weight from cart
        $totalWeight = 0;
        foreach ($this->cart as $item) {
            $totalWeight += ($item['weight'] ?? 0) * $item['qty'];
        }
        $totalWeight = max(1000, $totalWeight); // minimum 1kg

        $originCityId = (int) config('mlm.shipping.rajaongkir_origin', 151);
        $originType = config('mlm.shipping.rajaongkir_origin_type', 'city');

        $service = new RajaongkirService;
        $results = $service->getCost(
            $originCityId,
            $originType,
            (int) $this->cityId,
            'city', // fallback destination type
            $totalWeight,
            $this->selectedCourier
        );

        if (! empty($results) && isset($results[0]['costs'])) {
            $this->courierServices = $results[0]['costs'];
            $this->selectedService = $this->courierServices[0]['service'];
            $this->updatedSelectedService($this->selectedService);
        } else {
            $this->resetCourierCost();
        }
    }

    /**
     * Submit and place the order.
     */
    public function placeOrder(ProductOrderService $orderService)
    {
        $this->validate();

        $user = auth()->user();

        // Calculate Cart Totals
        $subtotal = 0.0;
        $totalBv = 0.0;
        $totalPoints = 0.0;
        $totalQty = 0;

        foreach ($this->cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
            $totalBv += $item['bv'] * $item['qty'];
            $totalPoints += $item['point'] * $item['qty'];
            $totalQty += $item['qty'];
        }

        // Calculate Stockist Discount
        $discount = (float) $orderService->calculateStockistDiscount($user, $subtotal);
        $totalCheckout = $subtotal - $discount + $this->courierCost;

        // Perform additional business rules validation
        // 1. Minimum order validation
        $minCheck = $orderService->validateCheckoutMinimumOrder($user, $subtotal);
        if (! $minCheck['valid']) {
            $this->dispatch('toast', variant: 'danger', heading: 'Minimum Belanja', content: $minCheck['message']);

            return;
        }

        // 2. Wallet balance validation
        if ($this->paymentMethod === 'wallet') {
            if ($user->ewalletBalance() < $totalCheckout) {
                $this->dispatch('toast', variant: 'danger', heading: 'Saldo Kurang', content: 'Saldo eWallet Anda tidak mencukupi untuk melakukan transaksi ini.');

                return;
            }

            if (! Hash::check($this->passwordConfirm, $user->password)) {
                $this->dispatch('toast', variant: 'danger', heading: 'Password Salah', content: 'Password konfirmasi salah.');

                return;
            }
        }

        // 3. Database transaction for placing the order
        try {
            $order = DB::transaction(function () use ($user, $subtotal, $discount, $totalBv, $totalPoints, $totalQty, $totalCheckout) {
                // Generate Invoice
                $invoice = 'INV/RO/'.now()->format('Ymd').'/'.strtoupper(Str::random(6));

                // Determine order status
                // eWallet paid orders are auto-confirmed (status = 1), transfer requires manual confirmation (status = 0)
                $status = $this->paymentMethod === 'wallet' ? 1 : 0;

                // Create Order
                $order = ProductOrder::create([
                    'invoice' => $invoice,
                    'member_id' => $user->id,
                    'stockist_id' => $this->productOrderTo === 'stockist' ? $this->selectStockistId : 0,
                    'type_order' => $user->type > 0 ? 'stockist' : 'member',
                    'status' => $status,
                    'point_reward' => $totalPoints,
                    'total_bv' => $totalBv,
                    'total_qty' => $totalQty,
                    'subtotal' => $subtotal,
                    'shipping' => $this->courierCost,
                    'discount' => $discount,
                    'total_checkout' => $totalCheckout,
                    'total_payment' => $this->paymentMethod === 'wallet' ? $totalCheckout : 0.0,
                    'payment_method' => $this->paymentMethod === 'wallet' ? 'wallet' : 'transfer',
                    'shipping_method' => $this->shippingMethod,
                    'shipping_courier' => $this->shippingMethod === 'ekspedisi' ? $this->selectedCourier : null,
                    'shipping_service' => $this->shippingMethod === 'ekspedisi' ? $this->selectedService : null,
                    'shipping_address' => $this->shippingMethod === 'ekspedisi' ? sprintf(
                        "%s\n%s, %s, %s, %s\nKode Pos: %s",
                        $this->name,
                        $this->address,
                        Village::find($this->villageId)?->name ?? '',
                        District::find($this->districtId)?->name ?? '',
                        City::find($this->cityId)?->name ?? '',
                        Province::find($this->provinceId)?->name ?? '',
                        Village::find($this->villageId)?->postal_code ?? ''
                    ) : 'Ambil Sendiri / Pick Up',
                    'products_json' => json_encode($this->cart),
                ]);

                // Create Order Details
                foreach ($this->cart as $item) {
                    ProductOrderDetail::create([
                        'product_order_id' => $order->id,
                        'member_id' => $user->id,
                        'product_id' => $item['product_id'],
                        'type' => $order->type_order,
                        'point' => $item['point'],
                        'bv' => $item['bv'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty'],
                        'subtotal_bv' => $item['bv'] * $item['qty'],
                    ]);
                }

                // If paid with e-wallet, create debit log
                if ($this->paymentMethod === 'wallet') {
                    EwalletLog::create([
                        'member_id' => $user->id,
                        'source_id' => $order->id,
                        'source' => 'order_payment',
                        'nominal' => $totalCheckout,
                        'amount' => $totalCheckout,
                        'type' => 'OUT',
                        'status' => 1,
                        'description' => 'Pembayaran pesanan produk dengan invoice '.$invoice,
                    ]);
                }

                return $order;
            });

            // Clear Cart Session
            session()->forget('shop_cart');
            $this->cart = [];

            $this->dispatch('toast', variant: 'success', heading: 'Sukses', content: 'Pesanan Anda berhasil dikirim.');

            return redirect()->route('shop.orders');

        } catch (\Exception $e) {
            Log::error('Placing order transaction failed', [
                'member_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('toast', variant: 'danger', heading: 'Error', content: 'Gagal membuat pesanan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();
        $stockists = Member::where('type', '>', 0)->where('status', 'active')->orderBy('name')->get();

        // Totals
        $subtotal = 0.0;
        $totalBv = 0.0;
        $totalPoints = 0.0;

        foreach ($this->cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
            $totalBv += $item['bv'] * $item['qty'];
            $totalPoints += $item['point'] * $item['qty'];
        }

        $orderService = new ProductOrderService;
        $discount = (float) $orderService->calculateStockistDiscount(auth()->user(), $subtotal);
        $totalCheckout = $subtotal - $discount + $this->courierCost;

        return view('livewire.shop.checkout', [
            'provinces' => $provinces,
            'stockists' => $stockists,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'totalCheckout' => $totalCheckout,
            'totalBv' => $totalBv,
            'totalPoints' => $totalPoints,
        ])->layout('layouts.app');
    }
}
