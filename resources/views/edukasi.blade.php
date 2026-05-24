<x-layouts::home :title="__('Edukasi')">

    @include('partials.home.heading', [
    'title' => __('Edukasi'),
    'imgbg' => asset('assets/img/carousel-3.png')
    ])

    {{-- ===== INTRO EDUKASI ===== --}}
    <div class="w-full bg-neutral-50 dark:bg-neutral-900 overflow-hidden py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex flex-col lg:flex-row items-center gap-12">

                {{-- Gambar --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="relative w-full rounded-3xl overflow-hidden shadow-xl" style="min-height: 400px;">
                        <img class="absolute inset-0 w-full h-full object-cover"
                            src="{{ asset('assets/img/training.png') }}" alt="Edukasi dan Pengembangan">
                    </div>
                </div>

                {{-- Teks --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.5s">
                    <div class="text-center lg:text-left">
                        <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">
                            Pilar Pertumbuhan
                        </h6>
                        <h1 class="text-4xl font-extrabold tracking-tight mb-6">
                            Edukasi dan Pengembangan
                        </h1>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-4">
                            Selain produk, Jagad Pesona Buana menyediakan berbagai pelatihan dan edukasi yang
                            mencakup pengembangan motivasi, kesadaran diri, terapi energi, pengetahuan penjualan
                            langsung profesional, serta digital marketing.
                        </p>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Edukasi ini bertujuan membangun mitra usaha yang memiliki fondasi mental, emosional,
                            dan keterampilan yang seimbang.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== PROGRAM EDUKASI ===== --}}
    <div class="w-full py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center mx-auto mb-16 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Kurikulum Kami</h6>
                <h1 class="text-4xl font-extrabold tracking-tight">Program Edukasi &amp; Pelatihan</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                [
                'delay' => '0.1s',
                'icon' => 'fa-brain',
                'title' => 'Motivasi & Kesadaran Diri',
                'desc' => 'Pelatihan pengembangan mental dan emosional untuk fondasi bisnis yang kuat dan kehidupan yang lebih bermakna.',
                ],
                [
                'delay' => '0.3s',
                'icon' => 'fa-bolt',
                'title' => 'Terapi Energi',
                'desc' => 'Edukasi terapi energi untuk keseimbangan fisik dan mental dalam kehidupan sehari-hari.',
                ],
                [
                'delay' => '0.5s',
                'icon' => 'fa-handshake',
                'title' => 'Direct Selling Profesional',
                'desc' => 'Pengetahuan dan keterampilan penjualan langsung yang etis, profesional, dan berkelanjutan.',
                ],
                [
                'delay' => '0.7s',
                'icon' => 'fa-laptop',
                'title' => 'Digital Marketing',
                'desc' => 'Strategi pemasaran digital untuk mengembangkan jaringan bisnis secara modern dan efektif.',
                ],
                ] as $item)
                <div class="wow fadeIn" data-wow-delay="{{ $item['delay'] }}">
                    <div class="bg-white dark:bg-neutral-800 rounded-3xl text-center p-8 h-full shadow-lg
                                border border-neutral-100 dark:border-neutral-700 hover:-translate-y-2
                                hover:shadow-xl transition-all duration-300 group">
                        <div class="w-16 h-16 bg-primary-50 dark:bg-neutral-700 rounded-2xl flex items-center
                                    justify-center mx-auto mb-6 group-hover:bg-primary-600 transition-all duration-300">
                            <i class="fa {{ $item['icon'] }} text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
                        </div>
                        <h5 class="mb-3 font-bold text-neutral-900 dark:text-white">{{ $item['title'] }}</h5>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 leading-relaxed">
                            {{ $item['desc'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>

</x-layouts::home>