<?php

namespace App\Livewire\Shop;

use App\Models\ProductOrder;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Riwayat Belanja Saya')]
class MemberOrders extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    // For viewing details in a modal
    public ?int $selectedOrderId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function viewDetails(int $orderId): void
    {
        $this->selectedOrderId = $orderId;
    }

    public function closeModal(): void
    {
        $this->selectedOrderId = null;
    }

    public function render()
    {
        $user = auth()->user();

        $query = ProductOrder::query()
            ->with(['details.product'])
            ->where('member_id', $user->id);

        if (! empty($this->search)) {
            $query->where('invoice', 'like', '%'.$this->search.'%');
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', (int) $this->statusFilter);
        }

        $orders = $query->latest()->paginate(10);

        $selectedOrder = $this->selectedOrderId
            ? ProductOrder::with(['details.product', 'details.package'])->find($this->selectedOrderId)
            : null;

        return view('livewire.shop.member-orders', [
            'orders' => $orders,
            'selectedOrder' => $selectedOrder,
        ])->layout('layouts.app');
    }
}
