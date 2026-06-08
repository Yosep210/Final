<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Shop Products')]
class ProductList extends Component
{
    public string $search = '';
    public string $type = 'all';
    
    // Active cart loaded from session
    public array $cart = [];

    // Selected product for modal details
    public ?int $selectedProductId = null;
    public string $selectedVariant = '';
    public int $quantity = 1;

    public function mount(): void
    {
        $this->cart = session()->get('shop_cart', []);
    }

    /**
     * Add an item to the shopping cart.
     */
    public function addToCart(int $productId): void
    {
        $product = Product::query()->where('status', true)->findOrFail($productId);
        
        $price = auth()->user()->type > 0 ? (float) $product->price : (float) $product->price_member;
        
        $cartKey = $productId;
        $variantLabel = '';

        if (! empty($product->varian)) {
            $variants = array_map('trim', explode(',', $product->varian));
            if (empty($this->selectedVariant)) {
                $this->selectedVariant = $variants[0];
            }
            $variantLabel = $this->selectedVariant;
            $cartKey = $productId . '_' . str_replace(' ', '_', $variantLabel);
        }

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['qty'] += $this->quantity;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'cart_key' => $cartKey,
                'name' => $product->name,
                'variant' => $variantLabel,
                'price' => $price,
                'qty' => $this->quantity,
                'weight' => (float) $product->weight,
                'bv' => (float) $product->bv,
                'point' => (float) $product->reward_point,
                'image' => $product->image,
            ];
        }

        $this->saveCart();
        $this->closeModal();
        $this->dispatch('toast', variant: 'success', heading: 'Berhasil', content: 'Produk ditambahkan ke keranjang.');
    }

    /**
     * Update item quantity in the cart.
     */
    public function updateQty(string $cartKey, int $qty): void
    {
        if ($qty <= 0) {
            $this->removeFromCart($cartKey);
            return;
        }

        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['qty'] = $qty;
            $this->saveCart();
        }
    }

    /**
     * Remove item from the cart.
     */
    public function removeFromCart(string $cartKey): void
    {
        unset($this->cart[$cartKey]);
        $this->saveCart();
        $this->dispatch('toast', variant: 'success', heading: 'Berhasil', content: 'Item dihapus dari keranjang.');
    }

    /**
     * Empty the shopping cart.
     */
    public function clearCart(): void
    {
        $this->cart = [];
        $this->saveCart();
        $this->dispatch('toast', heading: 'Keranjang dikosongkan.');
    }

    /**
     * Show product details modal.
     */
    public function showDetail(int $productId): void
    {
        $product = Product::query()->findOrFail($productId);
        $this->selectedProductId = $productId;
        $this->quantity = 1;
        
        if (! empty($product->varian)) {
            $variants = array_map('trim', explode(',', $product->varian));
            $this->selectedVariant = $variants[0];
        } else {
            $this->selectedVariant = '';
        }
    }

    public function closeModal(): void
    {
        $this->selectedProductId = null;
        $this->selectedVariant = '';
        $this->quantity = 1;
    }

    protected function saveCart(): void
    {
        session()->put('shop_cart', $this->cart);
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', variant: 'warning', heading: 'Keranjang Kosong', content: 'Silakan pilih produk terlebih dahulu.');
            return;
        }

        return redirect()->route('shop.checkout');
    }

    public function render()
    {
        $query = Product::query()
            ->where('status', true)
            ->where('show_order', true);

        if (! empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }

        $products = $query->latest()->paginate(12);

        $selectedProduct = $this->selectedProductId ? Product::find($this->selectedProductId) : null;

        // Calculate Cart Totals
        $totalPayment = 0;
        $totalBv = 0;
        $totalPoints = 0;
        $totalQty = 0;

        foreach ($this->cart as $item) {
            $totalPayment += $item['price'] * $item['qty'];
            $totalBv += $item['bv'] * $item['qty'];
            $totalPoints += $item['point'] * $item['qty'];
            $totalQty += $item['qty'];
        }

        return view('livewire.shop.product-list', [
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'totalPayment' => $totalPayment,
            'totalBv' => $totalBv,
            'totalPoints' => $totalPoints,
            'totalQty' => $totalQty,
        ])->layout('layouts.app');
    }
}
