<?php

namespace App\Livewire\Pin;

use App\Models\Member;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kirim Produk')]
class AdminIndex extends Component
{
    // Member Selection
    public string $targetUsername = '';

    public ?string $targetName = null;

    public ?int $targetId = null;

    // Product Selection
    public string $searchProduct = '';

    public bool $showProductModal = false;

    public array $selectedProducts = [];

    // Calculations
    public float $subtotal = 0;

    public float $discountPercent = 0;

    public float $discountAmount = 0;

    public float $totalPayment = 0;

    protected $queryString = [
        'targetUsername' => ['except' => ''],
        'searchProduct' => ['except' => ''],
    ];

    public function updating($property): void
    {
        if ($property === 'targetUsername') {
            $this->updatedTargetUsername();
        }

        if (in_array($property, ['discountPercent', 'discountAmount', 'selectedProducts'])) {
            $this->updateCalculations();
        }
    }

    public function updatedTargetUsername(): void
    {
        if ($this->targetUsername === '') {
            $this->targetName = null;
            $this->targetId = null;

            return;
        }

        $member = Member::query()->where('username', $this->targetUsername)->first();
        if ($member) {
            $this->targetName = $member->name;
            $this->targetId = $member->id;
        } else {
            $this->targetName = null;
            $this->targetId = null;
        }
    }

    public function openProductModal(): void
    {
        $this->showProductModal = true;
    }

    public function selectProduct(int $productId): void
    {
        // Get product from mock data (replace with real DB query)
        $product = $this->getProductById($productId);
        if ($product) {
            $this->selectedProducts[] = [
                'id' => $productId,
                'name' => $product['name'] ?? '',
                'variant' => $product['variant'] ?? '',
                'price' => $product['price'] ?? 0,
                'qty' => 1,
            ];

            $this->showProductModal = false;
            $this->updateCalculations();
            Flux::toast(variant: 'success', text: 'Produk ditambahkan ke daftar');
        }
    }

    public function removeProduct(int $index): void
    {
        unset($this->selectedProducts[$index]);
        $this->selectedProducts = array_values($this->selectedProducts);
        $this->updateCalculations();
    }

    public function updateCalculations(): void
    {
        // Calculate subtotal
        $this->subtotal = 0;
        foreach ($this->selectedProducts as $product) {
            $this->subtotal += ($product['price'] ?? 0) * ($product['qty'] ?? 1);
        }

        // Calculate discount
        if ($this->discountPercent > 0) {
            $this->discountAmount = ($this->subtotal * $this->discountPercent) / 100;
        }

        // Calculate total payment
        $this->totalPayment = $this->subtotal - $this->discountAmount;
    }

    public function sendProduct(): void
    {
        $this->validate([
            'targetId' => ['required', 'integer', 'min:1'],
            'selectedProducts' => ['required', 'array', 'min:1'],
        ]);

        // TODO: Implement actual product send logic here
        // This would save to database, create transaction records, etc.

        Flux::toast(variant: 'success', text: 'Produk berhasil dikirim ke member');
        $this->reset();
    }

    private function getProductById(int $productId): ?array
    {
        // Mock product data - replace with real DB query
        $products = [
            1 => ['id' => 1, 'name' => 'Produk A', 'variant' => 'Standar', 'price' => 100000],
            2 => ['id' => 2, 'name' => 'Produk B', 'variant' => 'Premium', 'price' => 150000],
            3 => ['id' => 3, 'name' => 'Produk C', 'variant' => 'Deluxe', 'price' => 200000],
        ];

        return $products[$productId] ?? null;
    }

    public function render()
    {
        // Get available products for modal
        $availableProducts = $this->getAvailableProducts();

        return view('livewire.pin.admin-index', [
            'availableProducts' => $availableProducts,
        ])->layout('layouts.app', ['title' => __('Kirim Produk')]);
    }

    private function getAvailableProducts(): array
    {
        // Mock products - replace with real DB query
        $allProducts = [
            ['id' => 1, 'name' => 'Produk A', 'variant' => 'Standar', 'price' => 100000],
            ['id' => 2, 'name' => 'Produk B', 'variant' => 'Premium', 'price' => 150000],
            ['id' => 3, 'name' => 'Produk C', 'variant' => 'Deluxe', 'price' => 200000],
            ['id' => 4, 'name' => 'Produk D', 'variant' => 'Super', 'price' => 250000],
        ];

        // Filter by search if applicable
        if ($this->searchProduct === '') {
            return $allProducts;
        }

        return array_filter($allProducts, function ($product) {
            return str_contains(strtolower($product['name']), strtolower($this->searchProduct)) ||
                str_contains(strtolower($product['variant']), strtolower($this->searchProduct));
        });
    }
}
