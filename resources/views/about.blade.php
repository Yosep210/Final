<x-layouts::home :title="__('About')">

    @include('partials.home.heading', [
    'title' => __('About'),
    'imgbg' => asset('assets/img/carousel-1.png')
    ])

    {{-- ===== TENTANG KAMI ===== --}}
    <div class="w-full bg-white dark:bg-neutral-900 overflow-hidden py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex flex-col lg:flex-row items-center gap-12">

                {{-- Gambar --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.2s">
                    <div class="relative w-full" style="min-height: 400px;">
                        <img class="absolute inset-0 w-full h-full object-cover rounded-[2.5rem] shadow-2xl"
                            src="{{ asset('assets/img/about.png') }}" alt="Tentang Jagad Pesona Buana">
                    </div>
                </div>

                {{-- Teks --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.4s">
                    <div class="text-center lg:text-left">
                        <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Tentang Kami</h6>
                        <h1 class="text-4xl font-extrabold tracking-tight mb-6">
                            Tentang Jagad Pesona Buana
                        </h1>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-4">
                            Jagad Pesona Buana merupakan perusahaan penjualan langsung yang berfokus pada
                            pengembangan gaya hidup sehat dan sejahtera. Kami hadir untuk menjawab kebutuhan
                            masyarakat modern akan produk perawatan diri, kesehatan, serta peluang usaha yang
                            dapat dijalankan secara fleksibel dan berkelanjutan.
                        </p>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-8">
                            Dengan pendekatan yang menggabungkan nilai kemanusiaan, edukasi, dan teknologi
                            digital, Jagad Pesona Buana membangun ekosistem bisnis yang tidak hanya berorientasi
                            pada penjualan, tetapi juga pada pertumbuhan pribadi dan komunitas.
                        </p>
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
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-neutral-900 dark:text-white">
                    Visi &amp; Misi Perusahaan
                </h1>
            </div>
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Visi --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="bg-white dark:bg-neutral-800 rounded-[2.5rem] p-10 h-full shadow-xl
                                shadow-neutral-200/50 dark:shadow-none hover:shadow-2xl hover:-translate-y-3
                                transition-all duration-500 group border border-neutral-100 dark:border-neutral-700">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="shrink-0 w-16 h-16 flex items-center justify-center bg-primary-50
                                        dark:bg-neutral-700 rounded-2xl group-hover:bg-primary-600 transition-all duration-500">
                                <i class="fa fa-eye text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Visi Perusahaan</h3>
                        </div>
                        <p class="text-neutral-500 dark:text-neutral-400 leading-loose">
                            Menjadi perusahaan penjualan langsung modern yang membangun masyarakat sehat
                            secara fisik, seimbang secara energi, dan mandiri secara finansial.
                        </p>
                    </div>
                </div>

                {{-- Misi --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.3s">
                    <div class="bg-white dark:bg-neutral-800 rounded-[2.5rem] p-10 h-full shadow-xl
                                shadow-neutral-200/50 dark:shadow-none hover:shadow-2xl hover:-translate-y-3
                                transition-all duration-500 group border border-neutral-100 dark:border-neutral-700">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="shrink-0 w-16 h-16 flex items-center justify-center bg-primary-50
                                        dark:bg-neutral-700 rounded-2xl group-hover:bg-primary-600 transition-all duration-500">
                                <i class="fa fa-bullseye text-2xl text-primary-600 group-hover:text-white transition-colors"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Misi Perusahaan</h3>
                        </div>
                        <ul class="space-y-3 text-neutral-500 dark:text-neutral-400">
                            @foreach ([
                            'Menyediakan produk perawatan diri dan kesehatan yang berkualitas',
                            'Membangun sistem bisnis penjualan langsung yang etis, transparan, dan berkelanjutan',
                            'Memberikan edukasi motivasi, kesadaran diri, dan pengembangan potensi manusia',
                            'Mengembangkan jaringan distribusi berbasis teknologi digital',
                            'Menciptakan peluang usaha yang relevan dengan perkembangan zaman',
                            ] as $misi)
                            <li class="flex items-start gap-3">
                                <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                                <span>{{ $misi }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

</x-layouts::home>