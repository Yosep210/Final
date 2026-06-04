<?php

namespace App\Livewire\ProductOrder;

use App\Models\ProductOrder;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Product Order')]
final class Index extends Component
{
    public bool $showModal = false;

    public ?ProductOrder $selectedOrder = null;

    #[On('product-order:view-detail')]
    public function viewDetail(int $orderId): void
    {
        $this->selectedOrder = ProductOrder::query()
            ->with(['member', 'details.product', 'details.package'])
            ->findOrFail($orderId);

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedOrder = null;
    }

    public function render()
    {
        return view('livewire.product-order.index')
            ->layout('layouts.app', ['title' => __('Product Order')]);
    }
}
