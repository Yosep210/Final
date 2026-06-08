<?php

namespace App\Livewire\ProductOrder;

use App\Models\EwalletLog;
use App\Models\ProductOrder;
use Illuminate\Support\Facades\DB;
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

    public function confirmOrder(int $orderId): void
    {
        $order = ProductOrder::findOrFail($orderId);

        if ($order->status === 0) {
            DB::transaction(function () use ($order) {
                $order->update([
                    'status' => 1, // Confirmed
                    'total_payment' => $order->total_checkout,
                ]);
            });

            // Reload details
            $this->viewDetail($orderId);
            $this->dispatch('toast', variant: 'success', heading: 'Sukses', content: 'Pesanan berhasil dikonfirmasi.');
        }
    }

    public function doneOrder(int $orderId): void
    {
        $order = ProductOrder::findOrFail($orderId);

        if ($order->status === 1) {
            DB::transaction(function () use ($order) {
                $order->update([
                    'status' => 2, // Done
                ]);
            });

            // Reload details
            $this->viewDetail($orderId);
            $this->dispatch('toast', variant: 'success', heading: 'Sukses', content: 'Pesanan ditandai selesai.');
        }
    }

    public function cancelOrder(int $orderId): void
    {
        $order = ProductOrder::findOrFail($orderId);

        if (in_array($order->status, [0, 1])) {
            DB::transaction(function () use ($order) {
                $order->update([
                    'status' => 4, // Cancelled
                ]);

                // Refund ewallet if paid with wallet and not refunded yet
                if ($order->payment_method === 'wallet') {
                    $refundExists = EwalletLog::where('member_id', $order->member_id)
                        ->where('source_id', $order->id)
                        ->where('source', 'order_refund')
                        ->exists();

                    if (! $refundExists) {
                        EwalletLog::create([
                            'member_id' => $order->member_id,
                            'source_id' => $order->id,
                            'source' => 'order_refund',
                            'nominal' => $order->total_checkout,
                            'amount' => $order->total_checkout,
                            'type' => 'IN',
                            'status' => 1,
                            'description' => 'Refund pembayaran pesanan produk '.$order->invoice.' karena dibatalkan.',
                        ]);
                    }
                }
            });

            // Reload details
            $this->viewDetail($orderId);
            $this->dispatch('toast', variant: 'success', heading: 'Sukses', content: 'Pesanan berhasil dibatalkan dan direfund (jika menggunakan eWallet).');
        }
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
