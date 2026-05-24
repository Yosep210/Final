<x-layouts::home :title="__('Opportunity')">

    @include('partials.home.heading', [
    'title' => __('Opportunity'),
    'imgbg' => asset('assets/img/carousel-2.png')
    ])

    <div class="w-full bg-neutral-50 dark:bg-neutral-900 overflow-hidden py-24">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex flex-col lg:flex-row items-center gap-16">

                {{-- Teks --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.1s">
                    <div class="text-center lg:text-left">
                        <h6 class="text-primary-600 font-bold uppercase mb-3 tracking-[0.2em] text-sm">
                            Model Bisnis Hybrid
                        </h6>
                        <h1 class="text-4xl font-extrabold tracking-tight mb-6">
                            Model Bisnis Hybrid
                        </h1>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-4">
                            Jagad Pesona Buana mengembangkan model bisnis hybrid yang menggabungkan metode
                            konvensional dan teknologi digital.
                        </p>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-4">
                            Pendekatan konvensional dilakukan melalui presentasi one-on-one, table talk,
                            seminar, dan pengembangan komunitas. Sementara itu, pemanfaatan teknologi digital
                            dilakukan melalui media sosial, aplikasi, landing page, dan sistem otomatisasi
                            untuk mendukung efektivitas jaringan.
                        </p>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed mb-8">
                            Model ini dirancang agar mitra dapat menjalankan bisnis secara fleksibel,
                            modern, dan berkelanjutan.
                        </p>
                        <div class="flex justify-center lg:justify-start">
                            <flux:button href="{{ route('edukasi') }}" variant="primary"
                                class="rounded-full !px-12 !py-4 shadow-lg">
                                Lihat Program Edukasi
                                <i class="fa fa-arrow-right ml-2 text-xs"></i>
                            </flux:button>
                        </div>
                    </div>
                </div>

                {{-- Gambar --}}
                <div class="w-full lg:w-1/2 wow fadeIn" data-wow-delay="0.5s">
                    <div class="relative w-full rounded-[2.5rem] overflow-hidden shadow-2xl"
                        style="min-height: 400px;">
                        <img class="absolute inset-0 w-full h-full object-cover"
                            src="{{ asset('assets/img/feature.png') }}" alt="Model Bisnis Hybrid">
                    </div>
                </div>

            </div>
        </div>
    </div>

</x-layouts::home>