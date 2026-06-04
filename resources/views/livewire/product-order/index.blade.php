<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Product Order') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage and monitor member product orders, invoice details, payments, and shipping.') }}
            </flux:text>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:product-order.product-order-table />
    </div>

    <flux:modal name="order-detail-modal" class="max-w-4xl md:min-w-4xl" wire:model="showModal"
        @close="$wire.closeModal()">
        @if($selectedOrder)
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                        <flux:heading size="lg" class="flex items-center gap-2">
                            {{ __('Order Detail') }} - <span class="font-mono text-zinc-900 dark:text-white">{{ $selectedOrder->invoice }}</span>
                        </flux:heading>
                        <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                            Ordered by <strong class="text-zinc-900 dark:text-white">{{ strtoupper($selectedOrder->member?->username) }}</strong> ({{ $selectedOrder->member?->name }}) on {{ $selectedOrder->created_at->format('d M Y H:i') }}
                        </flux:text>
                    </div>
                    <div class="mt-2 sm:mt-0">
                        @php
                            $statusClass = match ((int) $selectedOrder->status) {
                                1 => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                                2 => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                                4 => 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
                                default => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                            };
                            $statusText = match ((int) $selectedOrder->status) {
                                0 => 'Review',
                                1 => 'Confirmed',
                                2 => 'Done',
                                4 => 'Cancelled',
                                default => 'Unknown',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>

                <!-- Summary Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/50">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Payment Method') }}</div>
                        <div class="mt-1 font-semibold text-zinc-950 dark:text-white">{{ $selectedOrder->payment_method ?: '-' }}</div>
                    </div>
                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/50">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Shipping Method') }}</div>
                        <div class="mt-1 font-semibold text-zinc-950 dark:text-white">
                            {{ $selectedOrder->shipping_method ?: '-' }}
                            @if($selectedOrder->shipping_courier || $selectedOrder->shipping_service)
                                <span class="text-xs font-normal text-zinc-500">({{ strtoupper($selectedOrder->shipping_courier) }} {{ $selectedOrder->shipping_service }})</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/50">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Total BV') }}</div>
                        <div class="mt-1 font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($selectedOrder->total_bv, 2) }} BV</div>
                    </div>
                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800/50">
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Total Checkout') }}</div>
                        <div class="mt-1 font-semibold text-zinc-950 dark:text-white">Rp {{ number_format($selectedOrder->total_checkout, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Items Purchased -->
                <div>
                    <flux:heading size="md" class="mb-3">{{ __('Items Purchased') }}</flux:heading>
                    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-800">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th scope="col" class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-300">#</th>
                                    <th scope="col" class="px-4 py-2 text-left font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Product / Package') }}</th>
                                    <th scope="col" class="px-4 py-2 text-right font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Price') }}</th>
                                    <th scope="col" class="px-4 py-2 text-center font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Qty') }}</th>
                                    <th scope="col" class="px-4 py-2 text-right font-semibold text-zinc-700 dark:text-zinc-300">{{ __('BV') }}</th>
                                    <th scope="col" class="px-4 py-2 text-right font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Subtotal') }}</th>
                                    <th scope="col" class="px-4 py-2 text-right font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Subtotal BV') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                                @forelse($selectedOrder->details as $index => $detail)
                                    <tr>
                                        <td class="px-4 py-2 text-zinc-500 dark:text-zinc-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 font-medium text-zinc-900 dark:text-white">
                                            @if($detail->product_package_id > 0 && $detail->package)
                                                <span class="inline-flex items-center rounded-md bg-purple-50 px-1.5 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10 mr-1.5">Package</span>
                                                {{ $detail->package->name }}
                                            @elseif($detail->product_id > 0 && $detail->product)
                                                <span class="inline-flex items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 mr-1.5">Product</span>
                                                {{ $detail->product->name }}
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-zinc-50 px-1.5 py-0.5 text-xs font-medium text-zinc-700 ring-1 ring-inset ring-zinc-700/10 mr-1.5">{{ ucfirst($detail->type ?: 'Item') }}</span>
                                                Unknown Item
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right text-zinc-900 dark:text-white">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-center text-zinc-900 dark:text-white">{{ $detail->qty }}</td>
                                        <td class="px-4 py-2 text-right text-zinc-500 dark:text-zinc-400">{{ number_format($detail->bv, 2) }}</td>
                                        <td class="px-4 py-2 text-right text-zinc-900 dark:text-white font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-right text-emerald-600 dark:text-emerald-400 font-medium">{{ number_format($detail->subtotal_bv, 2) }} BV</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-center text-zinc-500 dark:text-zinc-400">{{ __('No items found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Details: Cost breakdown and Address -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Shipping Address -->
                    <div class="space-y-3">
                        <flux:heading size="md">{{ __('Shipping Address') }}</flux:heading>
                        <div class="rounded-lg border border-zinc-200 p-4 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-900/50 text-sm text-zinc-700 dark:text-zinc-300 min-h-[120px]">
                            @if($selectedOrder->shipping_address)
                                <div class="whitespace-pre-line font-medium leading-relaxed">{{ $selectedOrder->shipping_address }}</div>
                            @else
                                <div class="text-zinc-400 italic">{{ __('No shipping address provided.') }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Cost Breakdown -->
                    <div class="space-y-3">
                        <flux:heading size="md">{{ __('Cost Breakdown') }}</flux:heading>
                        <div class="rounded-lg border border-zinc-200 p-4 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-900/50 text-sm space-y-2">
                            <div class="flex justify-between">
                                <span class="text-zinc-500 dark:text-zinc-400">{{ __('Subtotal') }}</span>
                                <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($selectedOrder->shipping > 0)
                                <div class="flex justify-between">
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Shipping') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($selectedOrder->shipping, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->unique_code > 0)
                                <div class="flex justify-between">
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Unique Code') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($selectedOrder->unique_code, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->handling_fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Handling Fee') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($selectedOrder->handling_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->insurance_fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Insurance Fee') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($selectedOrder->insurance_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Fee') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($selectedOrder->fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->ppn > 0)
                                <div class="flex justify-between">
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('PPN') }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">Rp {{ number_format($selectedOrder->ppn, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->discount > 0)
                                <div class="flex justify-between text-red-600 dark:text-red-400">
                                    <span>{{ __('Discount') }}</span>
                                    <span class="font-semibold">- Rp {{ number_format($selectedOrder->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->voucher > 0)
                                <div class="flex justify-between text-red-600 dark:text-red-400">
                                    <span>{{ __('Voucher') }}</span>
                                    <span class="font-semibold">- Rp {{ number_format($selectedOrder->voucher, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($selectedOrder->autoro > 0)
                                <div class="flex justify-between text-red-600 dark:text-red-400">
                                    <span>{{ __('Auto RO Deduction') }}</span>
                                    <span class="font-semibold">- Rp {{ number_format($selectedOrder->autoro, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t border-zinc-200 dark:border-zinc-800 pt-2 flex justify-between text-base font-bold text-zinc-900 dark:text-white">
                                <span>{{ __('Total Checkout') }}</span>
                                <span>Rp {{ number_format($selectedOrder->total_checkout, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-semibold text-emerald-600 dark:text-emerald-400">
                                <span>{{ __('Total Payment') }}</span>
                                <span>Rp {{ number_format($selectedOrder->total_payment, 0, ',', '.') }}</span>
                            </div>
                            @if($selectedOrder->payment_remain > 0)
                                <div class="flex justify-between font-semibold text-red-600 dark:text-red-400">
                                    <span>{{ __('Remaining Payment') }}</span>
                                    <span>Rp {{ number_format($selectedOrder->payment_remain, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button type="button" variant="ghost" wire:click="closeModal">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
