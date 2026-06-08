<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700 pb-5">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white bg-gradient-to-r from-primary-600 to-indigo-600 bg-clip-text text-transparent">Toko Online JPBuana</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Belanja paket produk, upgrade, dan Repeat Order secara mudah.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-900 dark:to-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-3 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <flux:icon name="currency-dollar" class="w-5 h-5" />
                </div>
                <div>
                    <div class="text-xs text-zinc-400 font-medium">Saldo eWallet</div>
                    <div class="text-lg font-bold text-zinc-800 dark:text-zinc-100">Rp {{ number_format(auth()->user()->ewalletBalance(), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Product Grid (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Filter Bar --}}
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4">
                <div class="w-full sm:w-72">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama produk..." icon="magnifying-glass" />
                </div>
                <div class="flex gap-2 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
                    <button wire:click="$set('type', 'all')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $type === 'all' ? 'bg-primary-600 text-white shadow-md shadow-primary-600/10' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                        Semua
                    </button>
                    <button wire:click="$set('type', 'ro')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $type === 'ro' ? 'bg-primary-600 text-white shadow-md shadow-primary-600/10' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                        Repeat Order (RO)
                    </button>
                    <button wire:click="$set('type', 'perdana')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $type === 'perdana' ? 'bg-primary-600 text-white shadow-md shadow-primary-600/10' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                        Perdana
                    </button>
                    <button wire:click="$set('type', 'upgrade')" class="px-4 py-2 text-xs font-semibold rounded-xl transition-all {{ $type === 'upgrade' ? 'bg-primary-600 text-white shadow-md shadow-primary-600/10' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-750' }}">
                        Upgrade
                    </button>
                </div>
            </div>

            {{-- Products list --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($products as $product)
                    <div class="group bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-3xl overflow-hidden shadow-sm hover:shadow-md hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-300 flex flex-col justify-between">
                        {{-- Image / Header --}}
                        <div class="relative bg-zinc-50 dark:bg-zinc-950 aspect-video flex items-center justify-center overflow-hidden border-b border-zinc-100 dark:border-zinc-900">
                            @if ($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-indigo-600 opacity-20 flex items-center justify-center">
                                    <flux:icon name="shopping-bag" class="w-8 h-8 text-white" />
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center font-bold text-lg text-primary-600 dark:text-primary-400">
                                    {{ $product->sku }}
                                </div>
                            @endif
                            
                            {{-- Type badge --}}
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-sm {{
                                    $product->type === 'ro' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : (
                                    $product->type === 'upgrade' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' :
                                    'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300'
                                ) }}">
                                    {{ $product->type }}
                                </span>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <h3 class="font-bold text-zinc-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors text-base line-clamp-1" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-baseline gap-2 mt-2">
                                    <span class="text-lg font-extrabold text-zinc-900 dark:text-white">
                                        Rp {{ number_format(auth()->user()->type > 0 ? $product->price : $product->price_member, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-400 mt-2 line-clamp-2 leading-relaxed">
                                    {{ $product->description ?: 'Tidak ada deskripsi produk.' }}
                                </p>
                            </div>

                            <div class="space-y-3 pt-2">
                                <div class="flex items-center justify-between text-xs text-zinc-400 border-t border-zinc-100 dark:border-zinc-800/60 pt-3">
                                    <span>Volume: <strong>{{ number_format($product->bv, 0) }} BV</strong></span>
                                    <span>Poin: <strong>{{ number_format($product->reward_point, 2) }}</strong></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button wire:click="showDetail({{ $product->id }})" class="flex-1 py-2 px-3 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-750 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all">
                                        Detail
                                    </button>
                                    <button wire:click="addToCart({{ $product->id }})" class="flex-1 py-2 px-3 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-all shadow-md shadow-primary-600/10 hover:shadow-primary-600/20">
                                        + Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl">
                        <flux:icon name="shopping-bag" class="w-12 h-12 text-zinc-300 dark:text-zinc-700 mx-auto mb-4" />
                        <h3 class="font-bold text-zinc-800 dark:text-zinc-200">Tidak ada produk ditemukan</h3>
                        <p class="text-sm text-zinc-400 mt-1">Silakan cari kata kunci lain atau pilih filter yang berbeda.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $products->links() }}
            </div>
        </div>

        {{-- Sidebar Cart (4 cols) --}}
        <div class="lg:col-span-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 space-y-6 shadow-sm">
            <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4">
                <div class="flex items-center gap-2">
                    <flux:icon name="shopping-cart" class="w-5 h-5 text-zinc-400" />
                    <h2 class="font-bold text-zinc-800 dark:text-zinc-200 text-lg">Keranjang Belanja</h2>
                </div>
                <span class="bg-primary-100 text-primary-800 dark:bg-primary-950 dark:text-primary-300 text-xs font-bold px-2.5 py-1 rounded-full">
                    {{ $totalQty }} Item
                </span>
            </div>

            @if (empty($cart))
                <div class="py-12 text-center text-zinc-400 dark:text-zinc-600">
                    <flux:icon name="shopping-cart" class="w-10 h-10 mx-auto mb-3 opacity-40" />
                    <p class="text-sm">Keranjang Anda masih kosong.</p>
                    <p class="text-xs mt-1">Silakan pilih produk untuk ditambahkan.</p>
                </div>
            @else
                <div class="space-y-4 max-h-[360px] overflow-y-auto pr-1">
                    @foreach ($cart as $key => $item)
                        <div class="flex items-start justify-between gap-3 bg-white dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-900 rounded-2xl p-3 shadow-xs">
                            <div class="flex-1">
                                <h4 class="font-bold text-zinc-800 dark:text-zinc-200 text-sm line-clamp-1" title="{{ $item['name'] }}">{{ $item['name'] }}</h4>
                                @if ($item['variant'])
                                    <span class="inline-block mt-0.5 px-2 py-0.5 bg-zinc-100 dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 text-[10px] font-semibold rounded-md border border-zinc-200/40 dark:border-zinc-800/40">
                                        Varian: {{ $item['variant'] }}
                                    </span>
                                @endif
                                <div class="text-xs text-zinc-400 mt-1">Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}</div>
                                <div class="flex items-center gap-1.5 mt-2.5">
                                    <button wire:click="updateQty('{{ $key }}', {{ $item['qty'] - 1 }})" class="w-6 h-6 rounded-md bg-zinc-50 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 text-xs font-bold">-</button>
                                    <span class="text-xs font-semibold w-8 text-center text-zinc-800 dark:text-zinc-200">{{ $item['qty'] }}</span>
                                    <button wire:click="updateQty('{{ $key }}', {{ $item['qty'] + 1 }})" class="w-6 h-6 rounded-md bg-zinc-50 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 text-xs font-bold">+</button>
                                </div>
                            </div>
                            <div class="flex flex-col items-end justify-between h-full self-stretch">
                                <button wire:click="removeFromCart('{{ $key }}')" class="text-zinc-400 hover:text-rose-500 transition-colors p-1" title="Hapus">
                                    <flux:icon name="trash" class="w-4 h-4" />
                                </button>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200 text-sm">
                                    Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Summary --}}
                <div class="border-t border-zinc-200 dark:border-zinc-800 pt-4 space-y-3">
                    <div class="flex justify-between text-xs text-zinc-400">
                        <span>Total Volume:</span>
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($totalBv, 0) }} BV</span>
                    </div>
                    <div class="flex justify-between text-xs text-zinc-400">
                        <span>Total Poin:</span>
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($totalPoints, 2) }} Poin</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-zinc-800 dark:text-zinc-100 pt-1 border-t border-dashed border-zinc-200 dark:border-zinc-850">
                        <span>Total Belanja:</span>
                        <span>Rp {{ number_format($totalPayment, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button wire:click="clearCart" class="py-2.5 px-4 text-xs font-bold text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors bg-transparent border border-zinc-200 dark:border-zinc-700 rounded-2xl flex-1">
                            Batal
                        </button>
                        <button wire:click="checkout" class="py-2.5 px-5 text-xs font-bold text-white bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-750 hover:to-indigo-750 rounded-2xl flex-[2] transition-all shadow-md shadow-primary-600/10 hover:shadow-primary-600/20">
                            Checkout
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Modal --}}
    @if ($selectedProductId && $selectedProduct)
        <div class="fixed inset-0 z-55 flex items-center justify-center p-4 bg-zinc-950/50 backdrop-blur-xs transition-opacity duration-300">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl animate-in fade-in zoom-in-95 duration-200">
                <div class="flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800 px-6 py-4">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-lg">Detail Produk</h3>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1">
                        <flux:icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-5 aspect-square bg-zinc-50 dark:bg-zinc-950 border border-zinc-100 dark:border-zinc-900 rounded-2xl overflow-hidden flex items-center justify-center">
                        @if ($selectedProduct->image)
                            <img src="{{ asset($selectedProduct->image) }}" alt="{{ $selectedProduct->name }}" class="object-cover w-full h-full">
                        @else
                            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-primary-500 to-indigo-600 opacity-20 flex items-center justify-center">
                                <flux:icon name="shopping-bag" class="w-10 h-10 text-white" />
                            </div>
                        @endif
                    </div>
                    <div class="md:col-span-7 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <span class="px-2 py-0.5 bg-primary-50 text-primary-700 dark:bg-primary-950/30 dark:text-primary-400 text-[10px] font-bold uppercase tracking-wider rounded-md">
                                {{ $selectedProduct->type }}
                            </span>
                            <h2 class="font-bold text-zinc-900 dark:text-white text-xl">{{ $selectedProduct->name }}</h2>
                            <div class="text-2xl font-extrabold text-zinc-900 dark:text-white">
                                Rp {{ number_format(auth()->user()->type > 0 ? $selectedProduct->price : $selectedProduct->price_member, 0, ',', '.') }}
                            </div>
                            <p class="text-sm text-zinc-400 leading-relaxed pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                                {{ $selectedProduct->description ?: 'Tidak ada deskripsi produk.' }}
                            </p>
                        </div>

                        <div class="space-y-4">
                            @if ($selectedProduct->varian)
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Pilih Varian:</label>
                                    <select wire:model="selectedVariant" class="w-full text-sm font-medium border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 rounded-xl px-3 py-2">
                                        @foreach (array_map('trim', explode(',', $selectedProduct->varian)) as $var)
                                            <option value="{{ $var }}">{{ $var }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="flex items-center gap-4">
                                <div class="space-y-1.5 flex-1">
                                    <label class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Jumlah:</label>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="$set('quantity', {{ max(1, $quantity - 1) }})" class="w-9 h-9 rounded-xl bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 font-bold">-</button>
                                        <span class="text-sm font-semibold w-10 text-center text-zinc-800 dark:text-zinc-200">{{ $quantity }}</span>
                                        <button wire:click="$set('quantity', {{ $quantity + 1 }})" class="w-9 h-9 rounded-xl bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-500 font-bold">+</button>
                                    </div>
                                </div>
                                <div class="flex-1 pt-5">
                                    <button wire:click="addToCart({{ $selectedProduct->id }})" class="w-full py-2.5 px-4 text-xs font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-all shadow-md shadow-primary-600/10">
                                        Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
