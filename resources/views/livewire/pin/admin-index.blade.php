<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <!-- Header Section -->
    <div class="mb-6">
        <flux:heading size="xl">{{ __('Form Kirim Produk') }}</flux:heading>
        <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
            {{ __('Kirim produk kepada member') }}
        </flux:text>
    </div>

    <!-- Main Form -->
    <form wire:submit="sendProduct" class="space-y-6">
        <div
            class="bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700 rounded-xl p-6 space-y-6">

            <!-- Username Section -->
            <div>
                <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-3">
                    {{ __('Username') }} <span class="text-red-600">*</span>
                </label>
                <div class="flex gap-2">
                    <flux:input wire:model.live.debounce.300ms="targetUsername" placeholder="{{ __('Username') }}"
                        type="text" class="flex-1" />
                </div>
            </div>

            <!-- Member Info Display -->
            <div id="member_info">
                @if ($targetId)
                <div
                    class="p-4 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('Nama Member') }}</div>
                    <div class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $targetName }}</div>
                </div>
                @endif
            </div>

            <hr class="border-neutral-200 dark:border-neutral-700" />

            <!-- Product Selection Section -->
            <div>
                <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-3">
                    {{ __('Pilih Produk') }}
                </label>
                <div class="flex gap-2">
                    <flux:input wire:model.live.debounce.300ms="searchProduct" placeholder="{{ __('Cari Produk') }}"
                        class="flex-1" />
                </div>
            </div>

            <!-- Products Table -->
            <div>
                <div class="rounded-lg overflow-hidden border border-neutral-200 dark:border-neutral-700">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                                <th class="p-4 text-left font-semibold text-zinc-900 dark:text-white">{{ __('Produk') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($selectedProducts as $index => $product)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="p-4 text-zinc-900 dark:text-white">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">{{ $product['name'] ?? '-' }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $product['variant']
                                                ?? '-' }}</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <input type="number" wire:model="selectedProducts.{{ $index }}.qty" min="1"
                                                class="w-12 px-2 py-1 text-center border border-neutral-300 dark:border-neutral-600 rounded bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white text-xs" />
                                            <span class="text-xs text-zinc-600 dark:text-zinc-400 w-24 text-right">
                                                Rp {{ number_format(($product['price'] ?? 0) * ($product['qty'] ?? 1),
                                                0, ',', '.') }}
                                            </span>
                                            <button type="button" wire:click="removeProduct({{ $index }})"
                                                class="text-red-600 hover:text-red-700 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="p-4 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('No data available in table') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr
                                class="bg-zinc-50 dark:bg-zinc-800 border-t-2 border-neutral-200 dark:border-neutral-700">
                                <td class="p-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">{{
                                            __('Subtotal') }}</span>
                                        <span class="text-lg font-bold text-amber-600 dark:text-amber-400">Rp {{
                                            number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Discount Section (Hidden like reference) -->
            <div class="hidden">
                <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-2">
                    {{ __('Diskon') }}
                </label>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <flux:input wire:model.live="discountPercent" type="number" min="0" max="100" placeholder="0" />
                    </div>
                    <div>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">%</span>
                    </div>
                </div>
            </div>

            <!-- Total Payment Section -->
            <div>
                <label class="block text-sm font-semibold text-zinc-900 dark:text-white mb-3">
                    {{ __('Total Pembayaran') }}
                </label>
                <div class="flex gap-2">
                    <span
                        class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 rounded text-zinc-700 dark:text-zinc-400 text-sm">
                        {{ __('Rp') }}
                    </span>
                    <flux:input type="text" value="{{ number_format($totalPayment ?? 0, 0, ',', '.') }}" placeholder="0"
                        disabled class="flex-1" />
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center border-t border-neutral-200 dark:border-neutral-700 pt-6">
            <flux:button type="submit" variant="primary">
                <i class="fas fa-cart-plus mr-2"></i> {{ __('Kirim Produk') }}
            </flux:button>
        </div>
    </form>

    <!-- Product Selection Modal -->
    <flux:modal name="product-modal" class="max-w-2xl" wire:model="showProductModal">
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Pilih Produk') }}</flux:heading>
            <flux:input wire:model.live.debounce.300ms="searchProduct" placeholder="{{ __('Cari produk...') }}" />

            <div class="max-h-96 overflow-y-auto">
                <div class="space-y-2">
                    @forelse ($availableProducts as $product)
                    <button type="button" wire:click="selectProduct({{ $product['id'] ?? 0 }})"
                        class="w-full p-3 text-left border border-neutral-200 dark:border-neutral-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $product['name'] ?? '-' }}</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $product['variant'] ?? '-' }} - Rp {{ number_format($product['price'] ?? 0, 0, ',', '.')
                            }}
                        </div>
                    </button>
                    @empty
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        {{ __('Tidak ada produk ditemukan') }}
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                <flux:button type="button" variant="ghost" wire:click="$set('showProductModal', false)">
                    {{ __('Tutup') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>