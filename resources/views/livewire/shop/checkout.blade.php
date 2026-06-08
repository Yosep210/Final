<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-700 pb-5">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white bg-gradient-to-r from-primary-600 to-indigo-600 bg-clip-text text-transparent">Checkout Belanja</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Selesaikan pembelian produk Anda dengan mengisi formulir di bawah ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('shop.index') }}" wire:navigate class="py-2 px-4 text-xs font-semibold text-zinc-750 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-750 rounded-xl transition-all flex items-center gap-2">
                <flux:icon name="arrow-left" class="w-4 h-4" />
                Kembali ke Toko
            </a>
        </div>
    </div>

    <form wire:submit.prevent="placeOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Left: Checkout Form (8 cols) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Order Target Section (Pusat vs Stockist) --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 space-y-4 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white text-lg flex items-center gap-2">
                    <flux:icon name="building-office" class="w-5 h-5 text-zinc-400" />
                    Tujuan Pemesanan
                </h3>
                <p class="text-xs text-zinc-400">Pilih apakah pesanan dikirim dari Pusat atau melalui Stockist terdekat.</p>
                
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex flex-col p-4 border border-zinc-200 dark:border-zinc-850 rounded-2xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-all select-none">
                        <input type="radio" wire:model.live="productOrderTo" value="pusat" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200">Pusat (HQ)</span>
                            <div class="w-5 h-5 rounded-full border border-zinc-300 flex items-center justify-center {{ $productOrderTo === 'pusat' ? 'border-primary-500 bg-primary-500 text-white' : '' }}">
                                @if ($productOrderTo === 'pusat')
                                    <flux:icon name="check" class="w-3 h-3" />
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-zinc-400">Dikirim langsung dari gudang utama perusahaan.</span>
                    </label>

                    <label class="relative flex flex-col p-4 border border-zinc-200 dark:border-zinc-850 rounded-2xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-all select-none">
                        <input type="radio" wire:model.live="productOrderTo" value="stockist" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200">Stockist</span>
                            <div class="w-5 h-5 rounded-full border border-zinc-300 flex items-center justify-center {{ $productOrderTo === 'stockist' ? 'border-primary-500 bg-primary-500 text-white' : '' }}">
                                @if ($productOrderTo === 'stockist')
                                    <flux:icon name="check" class="w-3 h-3" />
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-zinc-400">Ambil/Kirim dari Stockist pilihan di sekitar daerah Anda.</span>
                    </label>
                </div>

                @if ($productOrderTo === 'stockist')
                    <div class="space-y-1.5 pt-2" wire:key="stockist-select-container">
                        <flux:select wire:model.live="selectStockistId" :label="__('Pilih Stockist')" placeholder="Pilih Stockist untuk melayani order Anda">
                            <flux:select.option value="">-- Silakan Pilih Stockist --</flux:select.option>
                            @foreach ($stockists as $stockist)
                                <flux:select.option value="{{ $stockist->id }}">{{ $stockist->name }} ({{ $stockist->username }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('selectStockistId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            {{-- Shipping Method Section (Pick Up vs Ekspedisi) --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 space-y-4 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white text-lg flex items-center gap-2">
                    <flux:icon name="truck" class="w-5 h-5 text-zinc-400" />
                    Metode Pengiriman
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex flex-col p-4 border border-zinc-200 dark:border-zinc-850 rounded-2xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-all select-none">
                        <input type="radio" wire:model.live="shippingMethod" value="pickup" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200">Ambil Sendiri (Pick Up)</span>
                            <div class="w-5 h-5 rounded-full border border-zinc-300 flex items-center justify-center {{ $shippingMethod === 'pickup' ? 'border-primary-500 bg-primary-500 text-white' : '' }}">
                                @if ($shippingMethod === 'pickup')
                                    <flux:icon name="check" class="w-3 h-3" />
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-zinc-400">Tidak ada biaya ongkir. Ambil sendiri di Pusat/Stockist.</span>
                    </label>

                    <label class="relative flex flex-col p-4 border border-zinc-200 dark:border-zinc-850 rounded-2xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-all select-none">
                        <input type="radio" wire:model.live="shippingMethod" value="ekspedisi" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200">Ekspedisi (Kirim Alamat)</span>
                            <div class="w-5 h-5 rounded-full border border-zinc-300 flex items-center justify-center {{ $shippingMethod === 'ekspedisi' ? 'border-primary-500 bg-primary-500 text-white' : '' }}">
                                @if ($shippingMethod === 'ekspedisi')
                                    <flux:icon name="check" class="w-3 h-3" />
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-zinc-400">Kirim ke alamat rumah Anda melalui kurir ekspedisi.</span>
                    </label>
                </div>

                @if ($shippingMethod === 'ekspedisi')
                    <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-850" wire:key="shipping-address-container">
                        <h4 class="font-semibold text-zinc-800 dark:text-zinc-200 text-sm">Alamat Pengiriman</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:input wire:model="name" :label="__('Nama Penerima')" placeholder="Nama Lengkap Penerima" />
                                @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </flux:field>
                            <flux:field>
                                <flux:input wire:model="phone" :label="__('No. Telepon')" placeholder="Contoh: 08123456789" />
                                @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:input wire:model="email" type="email" :label="__('Email Penerima')" placeholder="email@example.com" />
                            @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </flux:field>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div wire:key="province-select-container">
                                <flux:select wire:model.live="provinceId" :label="__('Provinsi')">
                                    <flux:select.option value="">-- Pilih Provinsi --</flux:select.option>
                                    @foreach($provinces as $province)
                                        <flux:select.option :value="$province->id">{{ $province->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('provinceId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div wire:key="city-select-container-{{ $provinceId }}">
                                <flux:select wire:model.live="cityId" :label="__('Kota / Kabupaten')" :disabled="empty($cities)">
                                    <flux:select.option value="">-- Pilih Kota / Kabupaten --</flux:select.option>
                                    @foreach($cities as $city)
                                        <flux:select.option :value="$city['id']">{{ $city['name'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('cityId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div wire:key="district-select-container-{{ $cityId }}">
                                <flux:select wire:model.live="districtId" :label="__('Kecamatan')" :disabled="empty($districts)">
                                    <flux:select.option value="">-- Pilih Kecamatan --</flux:select.option>
                                    @foreach($districts as $district)
                                        <flux:select.option :value="$district['id']">{{ $district['name'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('districtId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div wire:key="village-select-container-{{ $districtId }}">
                                <flux:select wire:model.live="villageId" :label="__('Kelurahan / Desa')" :disabled="empty($villages)">
                                    <flux:select.option value="">-- Pilih Kelurahan / Desa --</flux:select.option>
                                    @foreach($villages as $village)
                                        <flux:select.option :value="$village['id']">{{ $village['name'] }} ({{ $village['postal_code'] }})</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('villageId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <flux:field>
                            <flux:textarea wire:model="address" :label="__('Alamat Lengkap (Jalan, No. Rumah, RT/RW)')" placeholder="Alamat Detail" />
                            @error('address') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </flux:field>

                        {{-- Courier Selection --}}
                        <div class="border-t border-zinc-150 dark:border-zinc-800 pt-4 space-y-4">
                            <h4 class="font-semibold text-zinc-800 dark:text-zinc-200 text-sm">Pilih Ekspedisi & Kurir</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div wire:key="courier-select-container">
                                    <flux:select wire:model.live="selectedCourier" :label="__('Kurir')">
                                        <flux:select.option value="jne">JNE</flux:select.option>
                                        <flux:select.option value="jnt">J&T Express</flux:select.option>
                                        <flux:select.option value="pos">POS Indonesia</flux:select.option>
                                    </flux:select>
                                    @error('selectedCourier') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>

                                <div wire:key="service-select-container-{{ $cityId }}-{{ $selectedCourier }}-{{ count($courierServices) }}">
                                    <flux:select wire:model.live="selectedService" :label="__('Layanan Pengiriman')" :disabled="empty($courierServices)">
                                        <flux:select.option value="">-- Pilih Layanan / Layanan Fallback --</flux:select.option>
                                        @foreach($courierServices as $service)
                                            <flux:select.option :value="$service['service']">
                                                {{ $service['service'] }} ({{ $service['description'] }} - Rp {{ number_format($service['cost'][0]['value'], 0, ',', '.') }})
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    @error('selectedService') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
            </div>

            {{-- Payment Method Section --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 space-y-4 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white text-lg flex items-center gap-2">
                    <flux:icon name="credit-card" class="w-5 h-5 text-zinc-400" />
                    Metode Pembayaran
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex flex-col p-4 border border-zinc-200 dark:border-zinc-850 rounded-2xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-all select-none">
                        <input type="radio" wire:model.live="paymentMethod" value="transfer" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200">Transfer Bank</span>
                            <div class="w-5 h-5 rounded-full border border-zinc-300 flex items-center justify-center {{ $paymentMethod === 'transfer' ? 'border-primary-500 bg-primary-500 text-white' : '' }}">
                                @if ($paymentMethod === 'transfer')
                                    <flux:icon name="check" class="w-3 h-3" />
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-zinc-400">Perlu konfirmasi manual oleh admin setelah Anda melakukan transfer.</span>
                    </label>

                    <label class="relative flex flex-col p-4 border border-zinc-200 dark:border-zinc-850 rounded-2xl cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-950 transition-all select-none">
                        <input type="radio" wire:model.live="paymentMethod" value="wallet" class="sr-only">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm text-zinc-800 dark:text-zinc-200">eWallet Balance</span>
                            <div class="w-5 h-5 rounded-full border border-zinc-300 flex items-center justify-center {{ $paymentMethod === 'wallet' ? 'border-primary-500 bg-primary-500 text-white' : '' }}">
                                @if ($paymentMethod === 'wallet')
                                    <flux:icon name="check" class="w-3 h-3" />
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-zinc-400">Pembayaran instan dipotong dari saldo eWallet Anda. Langsung dikonfirmasi.</span>
                    </label>
                </div>

                @if ($paymentMethod === 'wallet')
                    <div class="space-y-3 pt-4 border-t border-zinc-100 dark:border-zinc-850" wire:key="wallet-password-container">
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-350 text-xs rounded-2xl border border-emerald-100 dark:border-emerald-900/40 flex items-center gap-3">
                            <flux:icon name="information-circle" class="w-5 h-5 flex-shrink-0" />
                            <div>
                                Saldo eWallet Saat Ini: <strong>Rp {{ number_format(auth()->user()->ewalletBalance(), 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        
                        <flux:field>
                            <flux:input wire:model="passwordConfirm" type="password" :label="__('Masukkan Password Akun')" placeholder="Password Anda untuk konfirmasi eWallet" />
                            @error('passwordConfirm') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </flux:field>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Order Summary (4 cols) --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Cart Items Summary --}}
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800/80 rounded-3xl p-6 space-y-6 shadow-sm">
                <div>
                    <h2 class="font-bold text-zinc-800 dark:text-zinc-200 text-lg">Ringkasan Pesanan</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Produk yang akan Anda bayar</p>
                </div>

                <div class="space-y-4 max-h-[280px] overflow-y-auto pr-1">
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
                            </div>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200 text-sm">
                                Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Summary Totals --}}
                <div class="border-t border-zinc-200 dark:border-zinc-800 pt-4 space-y-3">
                    <div class="flex justify-between text-xs text-zinc-400">
                        <span>Total Volume (BV):</span>
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($totalBv, 0) }} BV</span>
                    </div>
                    <div class="flex justify-between text-xs text-zinc-400">
                        <span>Total Poin Reward:</span>
                        <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ number_format($totalPoints, 2) }} Poin</span>
                    </div>

                    <div class="border-t border-dashed border-zinc-200 dark:border-zinc-800/80 my-2"></div>

                    <div class="flex justify-between text-sm text-zinc-500 dark:text-zinc-400">
                        <span>Subtotal Produk:</span>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-250">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if ($discount > 0)
                        <div class="flex justify-between text-sm text-emerald-600 dark:text-emerald-400">
                            <span>Diskon Stockist:</span>
                            <span class="font-semibold">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if ($shippingMethod === 'ekspedisi')
                        <div class="flex justify-between text-sm text-zinc-500 dark:text-zinc-400">
                            <span>Ongkos Kirim:</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-250">
                                @if ($courierCost > 0)
                                    Rp {{ number_format($courierCost, 0, ',', '.') }}
                                @else
                                    <span class="text-zinc-400 text-xs">Menunggu Alamat / Kurir</span>
                                @endif
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-between text-base font-bold text-zinc-850 dark:text-zinc-100 pt-2 border-t border-zinc-200 dark:border-zinc-850">
                        <span>Total Pembayaran:</span>
                        <span class="text-lg text-primary-600 dark:text-primary-400">Rp {{ number_format($totalCheckout, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 px-5 text-sm font-bold text-white bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-750 hover:to-indigo-750 rounded-2xl transition-all shadow-md shadow-primary-600/10 hover:shadow-primary-600/20 flex items-center justify-center gap-2">
                            <flux:icon name="lock-closed" class="w-4 h-4" />
                            Bayar & Selesaikan Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
