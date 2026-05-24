<x-layouts::home :title="__('Product')">

    @include('partials.home.heading', [
    'title' => __('Product'),
    'imgbg' => asset('assets/img/carousel-3.png')
    ])

    <div class="w-full py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">

            {{-- ===== HEADER ===== --}}
            <div class="text-center mx-auto mb-16 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Produk Unggulan</h6>
                <h1 class="text-4xl font-extrabold tracking-tight">Produk Jagad Pesona Buana</h1>
            </div>

            {{-- ===== LOVAURA ===== --}}
            <div class="flex flex-col lg:flex-row items-center gap-12 mb-24 wow fadeIn" data-wow-delay="0.1s">

                {{-- Gambar --}}
                <div class="w-full lg:w-1/2 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="flex items-center justify-center bg-neutral-50 dark:bg-neutral-800
                                rounded-[2.5rem] shadow-xl border border-neutral-100 dark:border-neutral-700
                                overflow-hidden group" style="height: 450px;">
                        <img class="max-h-full transition-transform duration-700 group-hover:scale-110"
                            src="{{ asset('assets/img/lovaura.png') }}" alt="LOVAURA Face Mist Aura Care">
                    </div>
                </div>

                {{-- Teks --}}
                <div class="w-full lg:w-1/2 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Tersedia Sekarang</h6>
                    <h1 class="text-5xl font-extrabold tracking-tight mb-2">LOVAURA</h1>
                    <h4 class="text-primary-600 font-semibold text-xl mb-6 tracking-widest uppercase text-sm">
                        Face Mist Aura Care
                    </h4>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-4">
                        LOVAURA merupakan produk pertama Jagad Pesona Buana yang dirancang sebagai face mist
                        untuk mendukung perawatan kulit dan kenyamanan diri dalam aktivitas sehari-hari.
                    </p>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-8">
                        Produk ini menjadi langkah awal Jagad Pesona Buana dalam menghadirkan rangkaian produk
                        gaya hidup sehat dan perawatan diri.
                    </p>
                    <flux:button href="{{ route('contact') }}" variant="primary"
                        class="rounded-full !px-10 !py-3 font-semibold shadow-lg">
                        Hubungi Kami <i class="fa fa-arrow-right ml-2 text-xs"></i>
                    </flux:button>
                </div>

            </div>

            {{-- ===== COMING SOON ===== --}}
            <div class="text-center mx-auto mb-12 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Produk Selanjutnya</h6>
                <h2 class="text-3xl font-extrabold tracking-tight mb-3">Segera Hadir</h2>
                <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed">
                    Kami sedang mempersiapkan rangkaian produk baru untuk melengkapi gaya hidup sehat Anda.
                </p>
            </div>

            <div class="flex flex-col lg:flex-row items-stretch gap-8 justify-center">

                @foreach ([
                [
                'delay' => '0.1s',
                'icon' => 'fa-leaf',
                'title' => 'Produk Kesehatan',
                'desc' => 'Fiber dan nutrisi pendukung untuk kesehatan tubuh yang optimal.',
                ],
                [
                'delay' => '0.3s',
                'icon' => 'fa-coffee',
                'title' => 'Minuman Kesehatan & Gaya Hidup',
                'desc' => 'Kopi, teh, dan minuman fungsional untuk gaya hidup sehat dan aktif.',
                ],
                ] as $item)
                <div class="w-full lg:w-1/2 flex flex-col wow fadeInUp" data-wow-delay="{{ $item['delay'] }}">
                    <div class="h-full flex flex-col rounded-[2.5rem] overflow-hidden bg-white dark:bg-neutral-800
                                shadow-lg border border-neutral-100 dark:border-neutral-700 group">

                        {{-- Icon area --}}
                        <div class="flex flex-col items-center justify-center bg-neutral-50 dark:bg-neutral-900/50
                                    group-hover:bg-primary-50 transition-colors duration-300" style="height: 240px;">
                            <i class="fa {{ $item['icon'] }} text-5xl text-neutral-300 group-hover:text-primary-400 transition-colors mb-3"></i>
                            <span class="text-xs font-bold tracking-widest uppercase text-primary-500 bg-primary-50
                                         dark:bg-primary-900/30 px-4 py-1.5 rounded-full">
                                Segera Hadir
                            </span>
                        </div>

                        {{-- Konten --}}
                        <div class="p-8 flex-1 flex flex-col">
                            <h4 class="text-xl font-bold mb-2">{{ $item['title'] }}</h4>
                            <p class="text-neutral-500 dark:text-neutral-400 text-sm leading-relaxed">
                                {{ $item['desc'] }}
                            </p>
                        </div>

                    </div>
                </div>
                @endforeach

            </div>

        </div>
    </div>

</x-layouts::home>