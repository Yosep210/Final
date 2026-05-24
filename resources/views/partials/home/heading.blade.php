<div class="relative w-full h-[300px] md:h-[400px] overflow-hidden mb-0">

    {{-- Background image --}}
    <img src="{{ $imgbg }}" alt="{{ $title }}"
        class="absolute inset-0 w-full h-full object-cover object-center">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/40 to-black/60"></div>

    {{-- Konten --}}
    <div class="relative z-10 h-full flex flex-col justify-center">
        <div class="max-w-7xl mx-auto w-full px-6 md:px-12">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 tracking-tight
                        opacity-0 translate-y-6 animate-[fadeUp_0.7s_ease_forwards]">
                {{ $title }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="flex items-center gap-2 text-white/80 text-sm md:text-base font-medium">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
                    </li>
                    <li class="text-white/40"><i class="fa fa-chevron-right text-xs"></i></li>
                    <li class="text-white" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
        </div>
    </div>

</div>