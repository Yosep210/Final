<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700 pb-5">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white bg-gradient-to-r from-primary-600 to-indigo-600 bg-clip-text text-transparent">Riwayat Belanja Saya</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Daftar pesanan produk dan status pengiriman Anda.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('shop.index') }}" wire:navigate class="py-2 px-4 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-all shadow-md shadow-primary-600/10 hover:shadow-primary-600/20 flex items-center gap-2">
                <flux:icon name="shopping-bag" class="w-4 h-4" />
                Mulai Belanja
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-sm">
        <div class="w-full sm:w-72">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nomor invoice..." icon="magnifying-glass" />
        </div>
        <div class="flex gap-2 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
            <button wire:click="$set('statusFilter', 'all')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $statusFilter === 'all' ? 'bg-primary-600 text-white shadow-md shadow-primary-600/10' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                Semua Status
            </button>
            <button wire:click="$set('statusFilter', '0')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $statusFilter === '0' ? 'bg-yellow-600 text-white shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                Review
            </button>
            <button wire:click="$set('statusFilter', '1')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $statusFilter === '1' ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                Confirmed
            </button>
            <button wire:click="$set('statusFilter', '2')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $statusFilter === '2' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                Done
            </button>
            <button wire:click="$set('statusFilter', '4')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $statusFilter === '4' ? 'bg-rose-600 text-white shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                Cancelled
            </button>
        </div>
    </div>

    {{-- Orders List --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 text-zinc-400 text-xs font-bold uppercase tracking-wider">
                        <th class="p-4 sm:p-5">Invoice</th>
                        <th class="p-4 sm:p-5">Tanggal</th>
                        <th class="p-4 sm:p-5">Tujuan</th>
                        <th class="p-4 sm:p-5 text-right">Total Belanja</th>
                        <th class="p-4 sm:p-5">Metode Bayar</th>
                        <th class="p-4 sm:p-5">Pengiriman</th>
                        <th class="p-4 sm:p-5 text-center">Status</th>
                        <th class="p-4 sm:p-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-850 text-zinc-700 dark:text-zinc-300 text-sm">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/20 transition-colors">
                            <td class="p-4 sm:p-5 font-bold text-zinc-900 dark:text-white">
                                {{ $order->invoice }}
                            </td>
                            <td class="p-4 sm:p-5 whitespace-nowrap">
                                {{ $order->created_at?->format('d M Y H:i') ?: '-' }}
                            </td>
                            <td class="p-4 sm:p-5 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-md {{ $order->stockist_id > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' }}">
                                    {{ $order->stockist_id > 0 ? 'Stockist' : 'Pusat' }}
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 font-semibold text-zinc-900 dark:text-white text-right whitespace-nowrap">
                                Rp {{ number_format($order->total_checkout, 0, ',', '.') }}
                            </td>
                            <td class="p-4 sm:p-5 whitespace-nowrap">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md border {{
                                    $order->payment_method === 'wallet' 
                                        ? 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950 dark:text-sky-300 dark:border-sky-900' 
                                        : 'bg-emerald-50 text-emerald-700 border-emerald-250 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-900'
                                }}">
                                    {{ strtoupper($order->payment_method) }}
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 whitespace-nowrap">
                                <span class="capitalize text-xs font-medium">
                                    {{ $order->shipping_method === 'ekspedisi' ? 'Ekspedisi (' . strtoupper($order->shipping_courier ?: '') . ')' : 'Pick Up' }}
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 text-center whitespace-nowrap">
                                @php
                                    $statusClass = match ((int) $order->status) {
                                        1 => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                                        2 => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                                        4 => 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
                                        default => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                                    };

                                    $statusText = match ((int) $order->status) {
                                        0 => 'Review',
                                        1 => 'Confirmed',
                                        2 => 'Done',
                                        4 => 'Cancelled',
                                        default => 'Unknown',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="p-4 sm:p-5 text-center whitespace-nowrap">
                                <button wire:click="viewDetails({{ $order->id }})" class="py-1.5 px-3 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-750 border border-zinc-200 dark:border-zinc-700 rounded-lg transition-all shadow-xs">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-zinc-400 dark:text-zinc-650">
                                <flux:icon name="shopping-bag" class="w-12 h-12 text-zinc-300 dark:text-zinc-750 mx-auto mb-4" />
                                <h3 class="font-bold text-zinc-800 dark:text-zinc-200">Tidak ada riwayat belanja</h3>
                                <p class="text-sm mt-1">Anda belum melakukan pesanan apa pun.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="p-4 sm:p-5 border-t border-zinc-100 dark:border-zinc-850">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if ($selectedOrderId && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-xs transition-all duration-300" wire:key="detail-modal-wrapper">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-3xl rounded-3xl overflow-hidden shadow-2xl animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
                
                {{-- Modal Header --}}
                <div class="flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800 px-6 py-4 flex-shrink-0">
                    <div>
                        <h3 class="font-bold text-zinc-900 dark:text-white text-lg">Detail Invoice {{ $selectedOrder->invoice }}</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Dipesan pada {{ $selectedOrder->created_at?->format('d M Y H:i') ?: '-' }}</p>
                    </div>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="p-6 space-y-6 overflow-y-auto flex-1">
                    {{-- Status block --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/50 dark:border-zinc-900 rounded-2xl p-4">
                            <span class="text-xs text-zinc-400 block mb-1">Status Pesanan</span>
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
                            <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold ring-1 ring-inset {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/50 dark:border-zinc-900 rounded-2xl p-4">
                            <span class="text-xs text-zinc-400 block mb-1">Metode Pembayaran</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ strtoupper($selectedOrder->payment_method) }}</span>
                            @if ($selectedOrder->payment_method === 'transfer' && $selectedOrder->status === 0)
                                <span class="text-[10px] text-yellow-600 dark:text-yellow-400 block mt-1">Harap transfer sebesar total pembayaran ke rekening perusahaan.</span>
                            @endif
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/50 dark:border-zinc-900 rounded-2xl p-4">
                            <span class="text-xs text-zinc-400 block mb-1">Poin & Volume</span>
                            <div class="text-xs text-zinc-650 dark:text-zinc-300">
                                Total BV: <strong class="text-zinc-800 dark:text-zinc-200">{{ number_format($selectedOrder->total_bv, 0) }} BV</strong><br>
                                Reward: <strong class="text-zinc-800 dark:text-zinc-200">{{ number_format($selectedOrder->point_reward, 2) }} Poin</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Address details --}}
                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/50 dark:border-zinc-900 rounded-2xl p-5 space-y-3">
                        <h4 class="font-bold text-zinc-850 dark:text-zinc-200 text-sm flex items-center gap-2">
                            <flux:icon name="truck" class="w-4 h-4 text-zinc-400" />
                            Informasi Pengiriman
                        </h4>
                        <div class="text-xs text-zinc-600 dark:text-zinc-400 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-zinc-400 block">Metode Pengiriman:</span>
                                <strong class="text-zinc-850 dark:text-zinc-200 text-sm capitalize">
                                    {{ $selectedOrder->shipping_method === 'ekspedisi' ? 'Kirim Alamat (Ekspedisi)' : 'Ambil Sendiri / Pick Up' }}
                                </strong>
                                @if ($selectedOrder->shipping_method === 'ekspedisi')
                                    <div class="mt-2 text-zinc-650 dark:text-zinc-350">
                                        Kurir: <strong class="text-zinc-800 dark:text-zinc-200">{{ strtoupper($selectedOrder->shipping_courier ?: '') }}</strong><br>
                                        Layanan: <strong class="text-zinc-800 dark:text-zinc-200">{{ strtoupper($selectedOrder->shipping_service ?: '') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <span class="text-zinc-400 block">Tujuan & Alamat:</span>
                                <div class="bg-white dark:bg-zinc-900/60 p-3 border border-zinc-150 dark:border-zinc-800 rounded-xl mt-1 text-zinc-750 dark:text-zinc-250 font-mono whitespace-pre-line text-[11px] leading-relaxed">
                                    {{ $selectedOrder->shipping_address ?: '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ordered Products list --}}
                    <div class="space-y-3">
                        <h4 class="font-bold text-zinc-850 dark:text-zinc-200 text-sm">Daftar Produk Dipesan</h4>
                        
                        <div class="border border-zinc-150 dark:border-zinc-800/80 rounded-2xl overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-150 dark:border-zinc-800 font-bold text-zinc-400 uppercase">
                                        <th class="p-3">Nama Produk</th>
                                        <th class="p-3 text-right">Harga Satuan</th>
                                        <th class="p-3 text-center">Qty</th>
                                        <th class="p-3 text-right">Subtotal</th>
                                        <th class="p-3 text-right">BV</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-850 text-zinc-700 dark:text-zinc-300">
                                    @foreach ($selectedOrder->details as $detail)
                                        <tr class="hover:bg-zinc-50/20 dark:hover:bg-zinc-950/10">
                                            <td class="p-3 font-semibold text-zinc-900 dark:text-white">
                                                @if ($detail->product)
                                                    {{ $detail->product->name }}
                                                @else
                                                    {{ ucfirst($detail->type ?: 'Produk') }}
                                                @endif
                                            </td>
                                            <td class="p-3 text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                            <td class="p-3 text-center font-bold text-zinc-900 dark:text-white">{{ $detail->qty }}</td>
                                            <td class="p-3 text-right font-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                            <td class="p-3 text-right">{{ number_format($detail->subtotal_bv, 0) }} BV</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-800 px-6 py-4 flex-shrink-0 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div class="text-xs text-zinc-450">
                        @if ($selectedOrder->stockist_id > 0)
                            Dilayani oleh Stockist ID: <strong>{{ $selectedOrder->stockist_id }}</strong>
                        @else
                            Dilayani oleh <strong>Pusat (HQ)</strong>
                        @endif
                    </div>
                    
                    {{-- Invoice Totals Breakdown --}}
                    <div class="space-y-1.5 text-sm text-right">
                        <div class="flex justify-between text-xs text-zinc-450">
                            <span>Subtotal:</span>
                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">Rp {{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if ($selectedOrder->discount > 0)
                            <div class="flex justify-between text-xs text-emerald-600 dark:text-emerald-400">
                                <span>Diskon Stockist:</span>
                                <span class="font-semibold">- Rp {{ number_format($selectedOrder->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($selectedOrder->shipping > 0)
                            <div class="flex justify-between text-xs text-zinc-450">
                                <span>Ongkir:</span>
                                <span class="font-semibold text-zinc-700 dark:text-zinc-300">Rp {{ number_format($selectedOrder->shipping, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm font-bold text-zinc-800 dark:text-zinc-150 border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-1">
                            <span>Total Pembayaran:</span>
                            <span class="text-primary-600 dark:text-primary-400">Rp {{ number_format($selectedOrder->total_checkout, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
