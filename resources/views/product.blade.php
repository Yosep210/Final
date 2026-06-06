<x-layouts::home :title="__('Product')">

    @include('partials.home.heading', [
    'title' => __('Produk Kami'),
    'imgbg' => asset('assets/img/carousel-3.webp')
    ])

    <div class="w-full py-24 bg-white dark:bg-neutral-900">
        <div class="max-w-7xl mx-auto px-6 md:px-12">

            {{-- ===== HEADER ===== --}}
            <div class="text-center mx-auto mb-16 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">Produk Unggulan</h6>
                <h1 class="text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-white">Produk Jagad Pesona
                    Buana</h1>
            </div>

            {{-- ===== SECTION 1: WORKSHOP ===== --}}
            <div class="flex flex-col lg:flex-row items-center gap-12 pb-16 mb-20 border-b border-neutral-100 dark:border-neutral-800/60 wow fadeIn"
                data-wow-delay="0.1s" id="produk-workshop">

                {{-- Gambar --}}
                <div class="w-full lg:w-4/12 wow fadeInUp" data-wow-delay="0.1s">
                    <div
                        class="rounded-4xl shadow-xl overflow-hidden w-full product-landing-slider-wrap product-landing-slider-wrap--workshop">
                        <div class="owl-carousel product-thumb-slider">
                            <div class="product-thumb-slide">
                                <img class="product-landing-slider-img"
                                    src="{{ asset('assets/img/FLYER-KUANTUMOASE.png') }}"
                                    alt="Kuantum Oase — flyer program">
                            </div>
                            <div class="product-thumb-slide">
                                <img class="product-landing-slider-img" src="{{ asset('assets/img/15.jpeg') }}"
                                    alt="B4 Formats">
                            </div>
                            <div class="product-thumb-slide">
                                <img class="product-landing-slider-img" src="{{ asset('assets/img/13.jpeg') }}"
                                    alt="Communion Symbol">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Teks --}}
                <div class="w-full lg:w-7/12 product-landing-copy">
                    <h6 class="text-primary-600 font-bold uppercase mb-2 tracking-[0.2em] text-sm">Kelas &amp;
                        Sertifikasi</h6>
                    <h2
                        class="text-3xl md:text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-white mb-3">
                        Workshop</h2>
                    <p class="text-primary-600 font-medium mb-4 text-base md:text-lg">Jalur Sertifikasi Praktisi
                        Internasional</p>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-6 text-base md:text-lg">
                        Pelatihan untuk melatih Anda sebagai praktisi energi: membersihkan medan energi dalam diri dan
                        di lingkungan, dengan pendampingan kurikulum terstruktur.
                    </p>
                    <ul class="space-y-3 mb-6 product-landing-list">
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Kuantum Oase</strong> — membuka ruang baru, peluang, dan resonansi yang
                                selaras dengan niat hidup.</span>
                        </li>
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>B4 Formats</strong> — memahami dan membersihkan berbagai relasi kehidupan
                                (rumah, pekerjaan, uang, tubuh, keluarga, memori).</span>
                        </li>
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Communion Symbol</strong> — menyelaraskan intuisi, kedalaman batin, dan
                                kesadaran spiritual.</span>
                        </li>
                    </ul>
                    <p class="text-xs text-neutral-500 dark:text-neutral-500 leading-relaxed mb-6">
                        Tiga kelas membentuk satu perjalanan: <strong>membuka → membersihkan → menyelaraskan</strong>.
                        Promo, silabus, dan jadwal tersedia di situs Workshop.
                    </p>
                    <a class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-full py-3.5 px-8 shadow-lg shadow-primary-600/10 hover:shadow-primary-600/20 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
                        href="https://workshop.jpbuana.com/" rel="noopener noreferrer" target="_blank"
                        aria-label="Selengkapnya tentang Workshop di situs resmi">
                        Selengkapnya di situs Workshop <i class="fa fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>

            </div>

            {{-- ===== SECTION 2: MINICON ===== --}}
            <div class="flex flex-col lg:flex-row-reverse items-center gap-12 pb-16 mb-20 border-b border-neutral-100 dark:border-neutral-800/60 wow fadeIn"
                data-wow-delay="0.1s" id="produk-minicon">

                {{-- Gambar --}}
                <div class="w-full lg:w-5/12">
                    <img class="w-full h-auto rounded-4xl shadow-xl product-landing-thumb"
                        src="{{ asset('assets/img/2.png') }}" alt="Minicon Car Voltage Stabilizer">
                </div>

                {{-- Teks --}}
                <div class="w-full lg:w-7/12 product-landing-copy">
                    <h6 class="text-primary-600 font-bold uppercase mb-2 tracking-[0.2em] text-sm">Efisiensi Kendaraan
                    </h6>
                    <h2
                        class="text-3xl md:text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-white mb-3">
                        Minicon</h2>
                    <p class="text-primary-600 font-medium mb-4 text-base md:text-lg">Car Voltage Stabilizer</p>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-6 text-base md:text-lg">
                        Solusi praktis menstabilkan kelistrikan kendaraan: pembakaran lebih optimal, komponen elektronik
                        lebih terlindungi, dan berkendara lebih nyaman—selaras kebutuhan efisiensi BBM.
                    </p>
                    <ul class="space-y-3 mb-6 product-landing-list">
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Teknologi inti</strong> — voltage stabilizer, battery doctor, dan interference
                                reducer.</span>
                        </li>
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Manfaat pengguna</strong> — BBM lebih terkendali, starter lebih ringan, AC dan
                                audio lebih stabil.</span>
                        </li>
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Pemasangan</strong> — praktis; panduan video dan detail produk di situs
                                Minicon.</span>
                        </li>
                    </ul>
                    <p class="text-xs text-neutral-500 dark:text-neutral-500 leading-relaxed mb-6">
                        Spesifikasi, testimoni, dan cara pasang lengkap tersedia di situs Minicon.
                    </p>
                    <a class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-full py-3.5 px-8 shadow-lg shadow-primary-600/10 hover:shadow-primary-600/20 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
                        href="https://minicon.jpbuana.com/" rel="noopener noreferrer" target="_blank"
                        aria-label="Selengkapnya tentang Minicon di situs resmi">
                        Selengkapnya di situs Minicon <i class="fa fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>

            {{-- ===== SECTION 3: PESONA AURA ===== --}}
            <div class="flex flex-col lg:flex-row items-center gap-12 mb-12 wow fadeIn" data-wow-delay="0.1s"
                id="produk-pesona-aura">

                {{-- Gambar --}}
                <div class="w-full lg:w-4/12 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="rounded-4xl shadow-xl overflow-hidden w-full product-landing-slider-wrap">
                        <div class="owl-carousel product-thumb-slider">
                            <div class="product-thumb-slide">
                                <img class="product-landing-slider-img" src="{{ asset('assets/img/14.png') }}"
                                    alt="Gelang Attraction">
                            </div>
                            <div class="product-thumb-slide">
                                <img class="product-landing-slider-img" src="{{ asset('assets/img/19.png') }}"
                                    alt="Gelang Protection">
                            </div>
                            <div class="product-thumb-slide">
                                <img class="product-landing-slider-img" src="{{ asset('assets/img/24.png') }}"
                                    alt="Topi Pesona Aura — Mind Clarity">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Teks --}}
                <div class="w-full lg:w-7/12 product-landing-copy">
                    <h6 class="text-primary-600 font-bold uppercase mb-2 tracking-[0.2em] text-sm">Wearable &amp;
                        Wellness</h6>
                    <h2
                        class="text-3xl md:text-4xl font-extrabold tracking-tight text-neutral-900 dark:text-white mb-3">
                        Pesona Aura</h2>
                    <p class="text-primary-600 font-medium mb-4 text-base md:text-lg">Gelang, Topi &amp; Teknologi
                        Bio-Energi</p>
                    <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-6 text-base md:text-lg">
                        Lini wearable dengan pendekatan spiritual dan sains: teknologi <strong>infrared</strong> serta
                        <strong>negative ion</strong> untuk kenyamanan tubuh dan keseimbangan energi sehari-hari.
                    </p>
                    <ul class="space-y-3 mb-6 product-landing-list">
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Gelang</strong> — varian putih (Attraction) dan hitam (Protection): daya
                                tarik, perlindungan energi, dan ketenangan.</span>
                        </li>
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Topi Mind Clarity</strong> — kenyamanan area kepala dan dukungan fokus saat
                                aktivitas panjang.</span>
                        </li>
                        <li class="flex items-start gap-3 text-neutral-600 dark:text-neutral-400">
                            <i class="fa fa-check-circle text-primary-500 mt-1 shrink-0"></i>
                            <span><strong>Gaya hidup</strong> — sejalan dengan preventive lifestyle dan perawatan diri
                                modern.</span>
                        </li>
                    </ul>
                    <p class="text-xs text-neutral-500 dark:text-neutral-500 leading-relaxed mb-6">
                        Varian produk, teknologi, dan testimoni tersedia di situs Pesona Aura.
                    </p>
                    <a class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-full py-3.5 px-8 shadow-lg shadow-primary-600/10 hover:shadow-primary-600/20 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
                        href="https://gelang.jpbuana.com/" rel="noopener noreferrer" target="_blank"
                        aria-label="Selengkapnya tentang Pesona Aura di situs resmi">
                        Selengkapnya di situs Pesona Aura <i class="fa fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>

            </div>

        </div>
    </div>

</x-layouts::home>