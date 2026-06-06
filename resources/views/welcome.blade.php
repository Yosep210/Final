<x-layouts::home :title="__('Home')">

    {{-- ===== HERO CAROUSEL ===== --}}
    <div class="w-full wow fadeIn" data-wow-delay="0.1s">
        <div class="owl-carousel header-carousel relative w-full overflow-hidden"
            style="height: clamp(420px, 56vw, 780px);">
            @include('partials.home.hero-slide', [
                'img' => asset('assets/img/carousel-1.webp'),
                'alt' => 'Build Your Healthy Life',
                'position' => 'center center',
            ])

            @include('partials.home.hero-slide', [
                'img' => asset('assets/img/carousel-2.webp'),
                'alt' => 'Build Your Healthy Life',
                'position' => 'center top',
            ])

            @include('partials.home.hero-slide', [
                'img' => asset('assets/img/carousel-3.webp'),
                'alt' => 'Build Your Healthy Life',
                'position' => 'center center',
            ])
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
                            src="{{ asset('assets/img/about.webp') }}" alt="Tentang Jagad Pesona Buana" loading="lazy"
                            style="min-height: 400px;">
                    </div>
                </div>

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.5s">
                    <div class="text-center lg:text-left">
                        <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Tentang Kami</h6>
                        <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tight mb-4">Jagad Pesona Buana</h1>
                        <p class="text-lg text-neutral-600 dark:text-neutral-400 mb-6 leading-relaxed">
                            Jagad Pesona Buana merupakan perusahaan penjualan langsung yang berfokus pada pengembangan
                            gaya hidup sehat dan
                            sejahtera. Kami hadir untuk menjawab kebutuhan masyarakat modern akan produk perawatan diri,
                            kesehatan, serta peluang
                            usaha yang dapat dijalankan secara fleksibel dan berkelanjungan.
                            </br>
                            </br>
                            Dengan pendekatan yang menggabungkan nilai kemanusiaan, edukasi, dan teknologi digital,
                            Jagad Pesona Buana membangun
                            ekosistem bisnis yang tidak hanya berorientasi pada penjualan, tetapi juga pada pertumbuhan
                            pribadi dan komunitas.
                        </p>
                        <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                            <flux:button href="{{ route('about') }}" variant="primary"
                                class="rounded-full px-10! py-3! font-semibold shadow-lg">
                                Kenali Kami
                            </flux:button>
                            <flux:button href="{{ route('opportunity') }}" variant="ghost"
                                class="rounded-full px-10! py-3! border-2 border-primary-600 text-primary-600 hover:bg-primary-50 transition-all">
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
                            <div
                                class="shrink-0 w-16 h-16 flex items-center justify-center bg-primary-50
                                        dark:bg-neutral-700 rounded-2xl group-hover:bg-primary-600 transition-all duration-500">
                                <i
                                    class="fa fa-eye text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
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
                            <div
                                class="shrink-0 w-16 h-16 flex items-center justify-center bg-primary-50
                                        dark:bg-neutral-700 rounded-2xl group-hover:bg-primary-600 transition-all duration-500">
                                <i
                                    class="fa fa-bullseye text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Misi Perusahaan</h3>
                        </div>
                        <ul class="space-y-3 text-neutral-500 dark:text-neutral-400 mb-6">
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Menyediakan produk perawatan diri dan kesehatan yang berkualitas</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Membangun sistem bisnis penjualan langsung yang etis, transparan, dan
                                    berkelanjutan</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Memberikan edukasi motivasi, kesadaran diri, dan pengembangan potensi
                                    manusia</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Mengembangkan jaringan distribusi berbasis teknologi digital</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>Menciptakan peluang usaha yang relevan dengan perkembangan zaman</span>
                            </li>
                        </ul>
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
                                <span class="text-xs font-bold text-neutral-400 tracking-widest uppercase">
                                    Workshop
                                    Kelas & Sertifikasi
                                </span>
                            </div>
                        </div>
                        <div class="service-icon">
                            <i class="fa fa-graduation-cap fa-3x"></i>
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <h4 class="text-xl font-bold mb-2">Kelas & Sertifikasi</h4>
                            <p class="text-neutral-400 text-sm">Kuantum Oase, B4 Formats, dan Communion Symbol dalam
                                workshop Menciptakan Realita Impian. Pelatihan praktisi energi
                                dengan jalur sertifikasi internasional.</p>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/3 flex flex-col wow fadeInUp" data-wow-delay="0.1s">
                    <div class="h-full flex flex-col group rounded-[2.5rem] overflow-hidden shadow-2xl
                                bg-white dark:bg-neutral-800 border border-neutral-100 dark:border-neutral-700">
                        <div class="overflow-hidden flex items-center justify-center bg-neutral-50"
                            style="height: 220px;">
                            <img class="min-h-full transition-transform duration-700 group-hover:scale-110"
                                src="{{ asset('assets/img/2.png') }}" alt="Minicon">
                        </div>
                        <div class="service-icon">
                            <i class="fa fa-car fa-3x"></i>
                        </div>
                        <div class="p-10 pt-6 flex-1 flex flex-col">
                            <h4 class="text-3xl font-bold mb-1">Minicon</h4>
                            <p class="text-primary-600 font-bold mb-4 tracking-widest text-xs uppercase">
                                Car Voltage Stabilizer
                            </p>
                            <p class="text-neutral-500 leading-relaxed mb-6 flex-1">
                                Stabilizer voltase untuk kelistrikan kendaraan yang stabil, efisiensi BBM, dan
                                kenyamanan berkendara. Panduan pemasangan
                                dan testimoni pengguna.
                            </p>
                            <div>
                                <flux:button href="{{ route('product') }}" variant="primary" class="rounded-full px-8!">
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
                                <span class="text-xs font-bold text-neutral-400 tracking-widest uppercase">
                                    Pesona Aura
                                </span>
                            </div>
                        </div>
                        <div class="service-icon">
                            <i class="fa fa-gem fa-3x"></i>
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <h4 class="text-xl font-bold mb-2">Gelang & Topi Bio-Energi</h4>
                            <p class="text-neutral-400 text-sm">Gelang dan topi dengan infrared serta negative ion untuk
                                kenyamanan tubuh. Wearable wellness mendukung fokus dan
                                keseimbangan energi.</p>
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
                        <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Inovasi Bisnis
                        </h6>
                        <h1 class="text-4xl font-extrabold tracking-tight mb-4">Model Bisnis Hybrid</h1>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed mb-4">
                            Jagad Pesona Buana mengembangkan model bisnis hybrid yang menggabungkan metode
                            konvensional dan teknologi digital.
                        </p>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-relaxed mb-6 hidden md:block">
                            Pendekatan konvensional dilakukan melalui presentasi one-on-one, table talk, seminar, dan
                            pengembangan komunitas. Sementara itu, pemanfaatan teknologi digital dilakukan melalui
                            media sosial, aplikasi, landing page, dan sistem otomatisasi untuk mendukung efektivitas
                            jaringan.
                        </p>
                        <flux:button href="{{ route('opportunity') }}" variant="primary" class="rounded-full px-8!">
                            Pelajari Lebih Lanjut <i class="fa fa-arrow-right ml-2"></i>
                        </flux:button>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.5s">
                    <div class="relative w-full rounded-3xl overflow-hidden shadow-xl" style="min-height: 380px;">
                        <img class="absolute inset-0 w-full h-full object-cover"
                            src="{{ asset('assets/img/feature.webp') }}" alt="Model Bisnis Hybrid">
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
                <flux:button href="{{ route('edukasi') }}" variant="primary" class="rounded-full px-8!">
                    Pelajari Lebih Lanjut <i class="fa fa-arrow-right ml-2"></i>
                </flux:button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                ['delay' => '0.1s', 'icon' => 'fa-brain', 'title' => 'Motivasi & Kesadaran Diri', 'desc' => 'Pelatihan
                pengembangan mental dan emosional untuk fondasi bisnis yang kuat.'],
                ['delay' => '0.3s', 'icon' => 'fa-bolt', 'title' => 'Terapi Energi', 'desc' => 'Edukasi terapi energi
                untuk keseimbangan fisik dan mental dalam kehidupan sehari-hari.'],
                ['delay' => '0.5s', 'icon' => 'fa-handshake', 'title' => 'Direct Selling Profesional', 'desc' =>
                'Pengetahuan dan keterampilan penjualan langsung yang etis dan profesional.'],
                ['delay' => '0.7s', 'icon' => 'fa-laptop', 'title' => 'Digital Marketing', 'desc' => 'Strategi pemasaran
                digital untuk mengembangkan jaringan bisnis secara modern.'],
                ] as $item)
                <div class="wow fadeIn" data-wow-delay="{{ $item['delay'] }}">
                    <div class="bg-white dark:bg-neutral-800 rounded-3xl p-6 text-center shadow-sm border
                                border-neutral-100 dark:border-neutral-700 hover:border-primary-300 transition-all
                                duration-300 group h-full">
                        <div class="w-16 h-16 flex items-center justify-center bg-neutral-100 dark:bg-neutral-700
                                    group-hover:bg-primary-600 rounded-2xl mx-auto mb-4 transition-all duration-300">
                            <i class="fa {{ $item['icon'] }} text-white"></i>
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
