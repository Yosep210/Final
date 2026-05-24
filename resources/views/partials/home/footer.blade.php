<footer class="bg-zinc-900 text-zinc-400 mt-5 pt-5 wow fadeIn px-4" data-wow-delay="0.1s">
    <div class="max-w-7xl mx-auto px-6 md:px-12 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Tentang Kami --}}
            <div>
                <h6 class="text-primary-400 font-bold uppercase mb-4 tracking-[0.2em] text-xs">Tentang Kami</h6>
                <p class="text-sm text-zinc-400 leading-relaxed">
                    Jagad Pesona Buana adalah perusahaan penjualan langsung berbasis gaya hidup sehat dan sejahtera
                    yang mengintegrasikan produk, edukasi, dan sistem bisnis modern untuk membangun masa depan yang
                    berkelanjutan.
                </p>
            </div>

            {{-- Alamat --}}
            <div>
                <h5 class="text-white font-semibold mb-4">Alamat</h5>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <i class="fa fa-map-marker-alt text-primary-400 mt-0.5 shrink-0"></i>
                        <span>Green Lake City, Rukan Crown Block E no 21, Cipondoh - Tangerang</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa fa-phone-alt text-primary-400 shrink-0"></i>
                        <a href="tel:+6281234567890" class="hover:text-white transition-colors">+62 8123-4567-890</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa fa-envelope text-primary-400 shrink-0"></i>
                        <a href="mailto:admin@jpbuana.com" class="hover:text-white transition-colors">admin@jpbuana.com</a>
                    </li>
                </ul>
                <div class="flex items-center gap-2 mt-5">
                    <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 hover:bg-primary-600 text-zinc-400 hover:text-white transition-all text-xs">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 hover:bg-primary-600 text-zinc-400 hover:text-white transition-all text-xs">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 hover:bg-primary-600 text-zinc-400 hover:text-white transition-all text-xs">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 hover:bg-primary-600 text-zinc-400 hover:text-white transition-all text-xs">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            {{-- Tautan Cepat --}}
            <div>
                <h5 class="text-white font-semibold mb-4">Tautan Cepat</h5>
                <ul class="space-y-2 text-sm">
                    @foreach ([
                    ['route' => 'home', 'label' => 'Home'],
                    ['route' => 'about', 'label' => 'Tentang Kami'],
                    ['route' => 'product', 'label' => 'Produk Unggulan'],
                    ['route' => 'opportunity', 'label' => 'Model Bisnis'],
                    ['route' => 'edukasi', 'label' => 'Edukasi & Pelatihan'],
                    ['route' => 'contact', 'label' => 'Kontak & Informasi'],
                    ['route' => 'login', 'label' => 'Login'],
                    ] as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                            class="flex items-center gap-2 text-zinc-400 hover:text-white hover:translate-x-1 transition-all duration-200">
                            <i class="fa fa-chevron-right text-xs text-primary-500"></i>
                            {{ $link['label'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Produk Kami --}}
            <div>
                <h5 class="text-white font-semibold mb-4">Produk Kami</h5>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <div class="shrink-0 w-8 h-8 flex items-center justify-center bg-primary-600/20 rounded-lg">
                            <i class="fa fa-spa text-primary-400 text-xs"></i>
                        </div>
                        <span class="pt-1">LOVAURA – Face Mist Aura Care</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="shrink-0 w-8 h-8 flex items-center justify-center bg-zinc-800 rounded-lg">
                            <i class="fa fa-leaf text-zinc-500 text-xs"></i>
                        </div>
                        <span class="pt-1">Produk Kesehatan
                            <span class="block text-xs text-zinc-600 mt-0.5">Segera Hadir</span>
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="shrink-0 w-8 h-8 flex items-center justify-center bg-zinc-800 rounded-lg">
                            <i class="fa fa-coffee text-zinc-500 text-xs"></i>
                        </div>
                        <span class="pt-1">Minuman Kesehatan
                            <span class="block text-xs text-zinc-600 mt-0.5">Segera Hadir</span>
                        </span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    {{-- Copyright --}}
    <div class="border-t border-zinc-800">
        <div class="max-w-7xl mx-auto px-6 md:px-12 py-5 flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-zinc-500">
            <span>
                © <a href="{{ route('home') }}" class="text-zinc-400 hover:text-white transition-colors">
                    {{ config('app.name', 'JPBuana') }}
                </a>, {{ date('Y') }} All Rights Reserved.
            </span>
            <span class="text-xs text-zinc-600">Jagad Pesona Buana</span>
        </div>
    </div>

    {{-- Back to Top --}}
    <a href="#"
        class="fixed bottom-6 right-6 z-50 w-11 h-11 flex items-center justify-center rounded-full bg-primary-600 text-white shadow-lg hover:bg-primary-700 hover:scale-110 transition-all duration-300"
        aria-label="Kembali ke atas">
        <i class="fa fa-arrow-up text-sm"></i>
    </a>

</footer>