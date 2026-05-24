<x-layouts::home :title="__('Home')">

    {{-- ===== HERO CAROUSEL ===== --}}
    <div class="w-full wow fadeIn" data-wow-delay="0.1s">
        <div class="owl-carousel header-carousel relative w-full overflow-hidden"
            style="height: clamp(420px, 56vw, 780px);">

            {{-- Slide 1 - landscape 16:9, focal point tengah --}}
            <div class="owl-carousel-item" data-dot="<img src='{{ asset('assets/img/carousel-1.png') }}'>">
                <img src="{{ asset('assets/img/carousel-1.png') }}" alt="Build Your Healthy Life"
                    style="object-position: center center;">
                <div class="owl-carousel-inner">
                    <div class="max-w-7xl mx-auto w-full px-6 md:px-12">
                        <div class="max-w-2xl text-center md:text-left">
                            <h1 class="text-white animated slideInDown font-extrabold mb-4 tracking-tight"
                                style="font-size: clamp(2rem, 7vw, 4.5rem); line-height: 1.1;">
                                Build Your <span class="text-primary-400">Healthy</span>
                                <br class="hidden md:block"> and Wealthy Life
                            </h1>
                            <p class="text-white/90 mb-8 animated slideInUp"
                                style="font-size: clamp(0.9rem, 2vw, 1.15rem); line-height: 1.7;">
                                Jagad Pesona Buana adalah perusahaan penjualan langsung berbasis
                                gaya hidup sehat, kesadaran diri, dan kemakmuran global.
                            </p>
                            <div class="animated slideInUp flex flex-wrap gap-3 justify-center md:justify-start">
                                <flux:button href="{{ route('about') }}" variant="primary"
                                    class="rounded-full !px-10 !py-4 shadow-xl shadow-primary-500/30 font-semibold">
                                    Kenali Kami
                                </flux:button>
                                <flux:button href="{{ route('opportunity') }}"
                                    class="rounded-full !px-10 !py-4 backdrop-blur-md bg-white/10 text-white border-white/30 hover:bg-white/20 transition-all font-semibold">
                                    Gabung Bersama Kami
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2 - portrait/square, focal point atas agar meja & tangan terlihat --}}
            <div class="owl-carousel-item" data-dot="<img src='{{ asset('assets/img/carousel-2.png') }}'>">
                <img src="{{ asset('assets/img/carousel-2.png') }}" alt="Solusi Bisnis Masa Depan"
                    style="object-position: center top;">
                <div class="owl-carousel-inner">
                    <div class="max-w-7xl mx-auto w-full px-6 md:px-12">
                        <div class="max-w-2xl text-center md:text-left">
                            <h1 class="text-white animated slideInDown font-extrabold mb-4 tracking-tight"
                                style="font-size: clamp(2rem, 7vw, 4.5rem); line-height: 1.1;">
                                Solusi Bisnis <br class="hidden md:block">
                                <span class="text-primary-400">Masa Depan</span>
                            </h1>
                            <p class="text-white/90 mb-8 animated slideInUp"
                                style="font-size: clamp(0.9rem, 2vw, 1.15rem); line-height: 1.7;">
                                Menggabungkan produk berkualitas tinggi dengan sistem edukasi bisnis
                                modern untuk pertumbuhan Anda.
                            </p>
                            <div class="animated slideInUp flex flex-wrap gap-3 justify-center md:justify-start">
                                <flux:button href="{{ route('about') }}" variant="primary"
                                    class="rounded-full !px-10 !py-4 shadow-xl shadow-primary-500/30 font-semibold">
                                    Kenali Kami
                                </flux:button>
                                <flux:button href="{{ route('opportunity') }}"
                                    class="rounded-full !px-10 !py-4 backdrop-blur-md bg-white/10 text-white border-white/30 hover:bg-white/20 transition-all font-semibold">
                                    Gabung Bersama Kami
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 3 - landscape, focal point tengah --}}
            <div class="owl-carousel-item" data-dot="<img src='{{ asset('assets/img/carousel-3.png') }}'>">
                <img src="{{ asset('assets/img/carousel-3.png') }}" alt="Komunitas Penuh Pesona"
                    style="object-position: center center;">
                <div class="owl-carousel-inner">
                    <div class="max-w-7xl mx-auto w-full px-6 md:px-12">
                        <div class="max-w-2xl text-center md:text-left">
                            <h1 class="text-white animated slideInDown font-extrabold mb-4 tracking-tight"
                                style="font-size: clamp(2rem, 7vw, 4.5rem); line-height: 1.1;">
                                Komunitas <br class="hidden md:block">
                                <span class="text-primary-400">Penuh Pesona</span>
                            </h1>
                            <p class="text-white/90 mb-8 animated slideInUp"
                                style="font-size: clamp(0.9rem, 2vw, 1.15rem); line-height: 1.7;">
                                Bergabunglah dengan ribuan orang yang telah memilih jalan hidup
                                lebih sehat dan mandiri secara finansial.
                            </p>
                            <div class="animated slideInUp flex flex-wrap gap-3 justify-center md:justify-start">
                                <flux:button href="{{ route('about') }}" variant="primary"
                                    class="rounded-full !px-10 !py-4 shadow-xl shadow-primary-500/30 font-semibold">
                                    Kenali Kami
                                </flux:button>
                                <flux:button href="{{ route('opportunity') }}"
                                    class="rounded-full !px-10 !py-4 backdrop-blur-md bg-white/10 text-white border-white/30 hover:bg-white/20 transition-all font-semibold">
                                    Gabung Bersama Kami
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== TENTANG KAMI ===== --}}
    <div class="w-full bg-white dark:bg-neutral-900 overflow-hidden py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex flex-col lg:flex-row items-center gap-12">

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="relative group" style="min-height: 400px;">
                        <div class="absolute -inset-4 bg-primary-100/50 dark:bg-primary-900/20 rounded-3xl -rotate-3
                                    transition-all duration-500 group-hover:rotate-0 group-hover:scale-105"></div>
                        <img class="relative w-full h-full object-cover rounded-3xl"
                            src="{{ asset('assets/img/about.png') }}"
                            alt="Tentang Jagad Pesona Buana" loading="lazy"
                            style="min-height: 400px;">
                    </div>
                </div>

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.5s">
                    <div class="text-center lg:text-left">
                        <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Tentang Kami</h6>
                        <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight mb-4">Jagad Pesona Buana</h1>
                        <p class="text-lg text-neutral-600 dark:text-neutral-400 mb-6 leading-relaxed">
                            Jagad Pesona Buana merupakan perusahaan penjualan langsung yang berfokus pada pengembangan
                            gaya hidup sehat dan sejahtera. Kami hadir untuk menjawab kebutuhan masyarakat modern akan
                            produk perawatan diri dan peluang usaha fleksibel.
                        </p>
                        <div class="space-y-4 mb-8">
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-9 h-9 flex items-center justify-center bg-primary-100 dark:bg-primary-900/30 rounded-lg text-primary-600">
                                    <i class="fa fa-heart"></i>
                                </div>
                                <p class="text-neutral-500 text-sm pt-2">Fokus pada kesehatan dan kesejahteraan holistik.</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-9 h-9 flex items-center justify-center bg-primary-100 dark:bg-primary-900/30 rounded-lg text-primary-600">
                                    <i class="fa fa-users"></i>
                                </div>
                                <p class="text-neutral-500 text-sm pt-2">Membangun komunitas yang edukatif dan produktif.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                            <flux:button href="{{ route('about') }}" variant="primary"
                                class="rounded-full !px-10 !py-3 font-semibold shadow-lg">
                                Kenali Kami
                            </flux:button>
                            <flux:button href="{{ route('opportunity') }}" variant="ghost"
                                class="rounded-full !px-10 !py-3 border-2 border-primary-600 text-primary-600 hover:bg-primary-50 transition-all">
                                Gabung Bersama Kami
                            </flux:button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== VISI & MISI ===== --}}
    <div class="w-full py-24 bg-neutral-50 dark:bg-neutral-900/50">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center mx-auto mb-16 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Visi &amp; Misi</h6>
                <h1 class="text-4xl font-extrabold tracking-tight">Tujuan &amp; Fondasi Kami</h1>
            </div>
            <div class="flex flex-col lg:flex-row gap-8">

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="bg-white dark:bg-neutral-800 rounded-[2.5rem] p-10 h-full shadow-xl shadow-neutral-200/50
                                dark:shadow-none hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 group
                                border border-neutral-100 dark:border-neutral-700">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="shrink-0 w-16 h-16 flex items-center justify-center bg-primary-50
                                        dark:bg-neutral-700 rounded-2xl group-hover:bg-primary-600 transition-all duration-500">
                                <i class="fa fa-eye text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Visi Perusahaan</h3>
                        </div>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-loose">
                            Menjadi perusahaan penjualan langsung modern yang membangun masyarakat sehat secara fisik,
                            seimbang secara energi, dan mandiri secara finansial.
                        </p>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.3s">
                    <div class="bg-white dark:bg-neutral-800 rounded-[2.5rem] p-10 h-full shadow-xl shadow-neutral-200/50
                                dark:shadow-none hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 group
                                border border-neutral-100 dark:border-neutral-700">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="shrink-0 w-16 h-16 flex items-center justify-center bg-primary-50
                                        dark:bg-neutral-700 rounded-2xl group-hover:bg-primary-600 transition-all duration-500">
                                <i class="fa fa-bullseye text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Misi Perusahaan</h3>
                        </div>
                        <ul class="space-y-3 text-neutral-500 dark:text-neutral-400 mb-6">
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Produk perawatan &amp; kesehatan berkualitas</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Sistem bisnis etis &amp; berkelanjutan</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Edukasi pengembangan potensi manusia</span>
                            </li>
                        </ul>
                        <flux:button variant="primary" href="{{ route('opportunity') }}"
                            class="rounded-full !px-10 !py-3 font-semibold shadow-lg">
                            Selengkapnya <i class="fa fa-arrow-right ml-2 text-xs"></i>
                        </flux:button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== PRODUK UNGGULAN ===== --}}
    <div class="w-full py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center mx-auto mb-16 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Produk Unggulan</h6>
                <h1 class="text-4xl font-extrabold tracking-tight">Inovasi Kesehatan</h1>
            </div>
            <div class="flex flex-col lg:flex-row items-stretch gap-8 justify-center">

                <div class="w-full lg:w-1/3 flex flex-col wow fadeInUp" data-wow-delay="0.3s">
                    <div class="h-full flex flex-col rounded-[2.5rem] overflow-hidden shadow-lg
                                bg-neutral-50 dark:bg-neutral-800/30 border-2 border-neutral-200
                                dark:border-neutral-700 group">
                        <div class="flex items-center justify-center bg-neutral-100 dark:bg-neutral-700/50
                                    group-hover:bg-primary-50 transition-colors" style="height: 220px;">
                            <div class="text-center p-3">
                                <i class="fa fa-leaf text-5xl text-neutral-300 group-hover:text-primary-500 transition-colors mb-3 block"></i>
                                <span class="text-xs font-bold text-neutral-400 tracking-widest uppercase">Segera Hadir</span>
                            </div>
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <h4 class="text-xl font-bold mb-2">Kesehatan</h4>
                            <p class="text-neutral-400 text-sm">Nutrisi pendukung tubuh yang optimal.</p>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/3 flex flex-col wow fadeInUp" data-wow-delay="0.1s">
                    <div class="h-full flex flex-col group rounded-[2.5rem] overflow-hidden shadow-2xl
                                bg-white dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                        <div class="overflow-hidden flex items-center justify-center bg-neutral-50" style="height: 220px;">
                            <img class="max-h-full transition-transform duration-700 group-hover:scale-110"
                                src="{{ asset('assets/img/lovaura.png') }}" alt="LOVAURA Face Mist">
                        </div>
                        <div class="p-10 pt-6 flex-1 flex flex-col">
                            <h4 class="text-3xl font-bold mb-1">LOVAURA</h4>
                            <p class="text-primary-600 font-bold mb-4 tracking-widest text-xs uppercase">Face Mist Aura Care</p>
                            <p class="text-neutral-500 leading-relaxed mb-6 flex-1">
                                Diciptakan untuk mendukung perawatan kulit dan keseimbangan energi harian Anda.
                            </p>
                            <div>
                                <flux:button href="{{ route('product') }}" variant="primary" class="rounded-full !px-8">
                                    Lihat Detail <i class="fa fa-arrow-right ml-2"></i>
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/3 flex flex-col wow fadeInUp" data-wow-delay="0.5s">
                    <div class="h-full flex flex-col rounded-[2.5rem] overflow-hidden shadow-lg
                                bg-neutral-50 dark:bg-neutral-800/30 border-2 border-neutral-200
                                dark:border-neutral-700 group">
                        <div class="flex items-center justify-center bg-neutral-100 dark:bg-neutral-700/50
                                    group-hover:bg-primary-50 transition-colors" style="height: 220px;">
                            <div class="text-center p-3">
                                <i class="fa fa-coffee text-5xl text-neutral-300 group-hover:text-primary-500 transition-colors mb-3 block"></i>
                                <span class="text-xs font-bold text-neutral-400 tracking-widest uppercase">Segera Hadir</span>
                            </div>
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <h4 class="text-xl font-bold mb-2">Minuman</h4>
                            <p class="text-neutral-400 text-sm">Varian kopi dan teh fungsional.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== INOVASI BISNIS ===== --}}
    <div class="w-full bg-neutral-50 dark:bg-neutral-900 overflow-hidden py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex flex-col lg:flex-row items-center gap-12">

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="text-center lg:text-left">
                        <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Inovasi Bisnis</h6>
                        <h1 class="text-4xl font-extrabold tracking-tight mb-4">Model Bisnis Hybrid</h1>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed mb-4">
                            Jagad Pesona Buana mengembangkan model bisnis hybrid yang menggabungkan metode
                            konvensional dan teknologi digital.
                        </p>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed mb-6 hidden md:block">
                            Pendekatan konvensional dilakukan melalui presentasi one-on-one, table talk, seminar, dan
                            pengembangan komunitas. Sementara itu, pemanfaatan teknologi digital dilakukan melalui
                            media sosial, aplikasi, landing page, dan sistem otomatisasi untuk mendukung efektivitas jaringan.
                        </p>
                        <flux:button href="{{ route('opportunity') }}" variant="primary" class="rounded-full !px-8">
                            Pelajari Lebih Lanjut <i class="fa fa-arrow-right ml-2"></i>
                        </flux:button>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.5s">
                    <div class="relative w-full rounded-3xl overflow-hidden shadow-xl" style="min-height: 380px;">
                        <img class="absolute inset-0 w-full h-full object-cover"
                            src="{{ asset('assets/img/feature.png') }}" alt="Model Bisnis Hybrid">
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== EDUKASI & PENGEMBANGAN ===== --}}
    <div class="w-full py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center mx-auto mb-16 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Pilar Pertumbuhan</h6>
                <h1 class="text-4xl font-extrabold tracking-tight mb-4">Edukasi &amp; Pengembangan</h1>
                <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed mb-4">
                    Selain produk, Jagad Pesona Buana menyediakan berbagai pelatihan dan edukasi yang mencakup
                    pengembangan motivasi, kesadaran diri, terapi energi, pengetahuan penjualan langsung profesional,
                    serta digital marketing.
                </p>
                <flux:button href="{{ route('edukasi') }}" variant="primary" class="rounded-full !px-8">
                    Pelajari Kurikulum Kami <i class="fa fa-arrow-right ml-2"></i>
                </flux:button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                ['delay' => '0.1s', 'icon' => 'fa-brain', 'title' => 'Motivasi', 'desc' => 'Pengembangan mental dan emosional untuk fondasi bisnis yang kuat.'],
                ['delay' => '0.3s', 'icon' => 'fa-bolt', 'title' => 'Terapi Energi', 'desc' => 'Keseimbangan fisik dan mental untuk performa maksimal dalam kehidupan sehari-hari.'],
                ['delay' => '0.5s', 'icon' => 'fa-handshake', 'title' => 'Direct Selling', 'desc' => 'Keterampilan penjualan langsung yang etis, modern, dan profesional.'],
                ['delay' => '0.7s', 'icon' => 'fa-laptop', 'title' => 'Digital Marketing', 'desc' => 'Strategi pemasaran digital untuk mengembangkan jaringan bisnis global.'],
                ] as $item)
                <div class="wow fadeIn" data-wow-delay="{{ $item['delay'] }}">
                    <div class="bg-white dark:bg-neutral-800 rounded-3xl p-6 text-center shadow-sm border
                                border-neutral-100 dark:border-neutral-700 hover:border-primary-300 transition-all
                                duration-300 group h-full">
                        <div class="w-16 h-16 flex items-center justify-center bg-neutral-100 dark:bg-neutral-700
                                    group-hover:bg-primary-600 rounded-2xl mx-auto mb-4 transition-all duration-300">
                            <i class="fa {{ $item['icon'] }} text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
                        </div>
                        <h5 class="text-base font-bold mb-3">{{ $item['title'] }}</h5>
                        <p class="text-neutral-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</x-layouts::home>