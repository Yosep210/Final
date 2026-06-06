<div class="owl-carousel-item" data-dot="<img src='{{ $img }}'>">
    <img src="{{ $img }}" alt="{{ $alt }}" style="object-position: {{ $position }};">
    <div class="owl-carousel-inner">
        <div class="max-w-7xl mx-auto w-full px-6 md:px-12">
            <div class="max-w-2xl text-center md:text-left">
                <h1 class="text-white animated slideInDown font-extrabold mb-4 tracking-tight"
                    style="font-size: clamp(2rem, 7vw, 4.5rem); line-height: 1.1;">
                    Build Your Healthy
                    <br class="hidden md:block"> and Wealthy Life
                </h1>
                <p class="text-white/90 mb-8 animated slideInUp"
                    style="font-size: clamp(0.9rem, 2vw, 1.15rem); line-height: 1.7;">
                    Jagad Pesona Buana adalah perusahaan penjualan langsung berbasis gaya hidup sehat,
                    kesadaran diri, dan kemakmuran, yang menggabungkan produk berkualitas, edukasi,
                    dan sistem bisnis modern.
                </p>
                <div class="animated slideInUp flex flex-wrap gap-3 justify-center md:justify-start">
                    <flux:button href="{{ route('about') }}" variant="primary"
                        class="rounded-full px-10! py-4! shadow-xl shadow-primary-500/30 font-semibold">
                        Kenali Kami
                    </flux:button>
                    <flux:button href="{{ route('opportunity') }}"
                        class="rounded-full px-10! py-4! backdrop-blur-md bg-white/10 text-white border-white/30 hover:bg-white/20 transition-all font-semibold">
                        Gabung Bersama Kami
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>