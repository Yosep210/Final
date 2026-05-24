{{-- ===== TOPBAR ===== --}}
<div class="hidden lg:block border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-6 flex w-full items-center justify-between py-2 text-sm text-zinc-600 dark:text-zinc-400">

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <flux:icon.map-pin class="size-4 shrink-0" />
                <span>Green Lake City, Rukan Crown Block E no 21, Cipondoh - Tangerang</span>
            </div>
            <div class="flex items-center gap-2">
                <flux:icon.envelope class="size-4 shrink-0" />
                <a href="mailto:admin@jpbuana.com" class="hover:text-primary-600 transition-colors">admin@jpbuana.com</a>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 border-r border-zinc-200 dark:border-zinc-700 pr-6">
                <flux:icon.phone class="size-4 shrink-0" />
                <a href="tel:+6281234567890" class="font-medium hover:text-primary-600 transition-colors">+62 8123-4567-890</a>
            </div>
            <div class="flex items-center gap-2">
                <flux:button href="#" variant="ghost" size="sm" class="!p-1.5"><i class="fab fa-facebook-f text-xs"></i></flux:button>
                <flux:button href="#" variant="ghost" size="sm" class="!p-1.5"><i class="fab fa-twitter text-xs"></i></flux:button>
                <flux:button href="#" variant="ghost" size="sm" class="!p-1.5"><i class="fab fa-linkedin text-xs"></i></flux:button>
                <flux:button href="#" variant="ghost" size="sm" class="!p-1.5"><i class="fab fa-instagram text-xs"></i></flux:button>
            </div>
        </div>

    </div>
</div>

{{-- ===== NAVBAR ===== --}}
<flux:header container class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 shadow-sm">

    <x-app-logo class="mt-2" href="{{ route('home') }}" wire:navigate />

    <flux:spacer />

    {{-- Desktop --}}
    <flux:navbar class="hidden lg:flex me-1.5 space-x-0.5 rtl:space-x-reverse !py-0 mt-2">
        <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
            {{ __('Home') }}
        </flux:navbar.item>
        <flux:navbar.item :href="route('about')" :current="request()->routeIs('about')" wire:navigate>
            {{ __('About') }}
        </flux:navbar.item>
        <flux:navbar.item :href="route('product')" :current="request()->routeIs('product')" wire:navigate>
            {{ __('Product') }}
        </flux:navbar.item>
        <flux:navbar.item :href="route('opportunity')" :current="request()->routeIs('opportunity')" wire:navigate>
            {{ __('Opportunity') }}
        </flux:navbar.item>
        <flux:navbar.item :href="route('edukasi')" :current="request()->routeIs('edukasi')" wire:navigate>
            {{ __('Edukasi') }}
        </flux:navbar.item>
        <flux:navbar.item :href="route('contact')" :current="request()->routeIs('contact')" wire:navigate>
            {{ __('Contact') }}
        </flux:navbar.item>
    </flux:navbar>

    {{-- Tombol Login (Desktop) --}}
    <flux:button href="{{ route('login') }}" variant="primary" size="sm"
        class="hidden lg:flex rounded-full !px-6 ml-3 mt-2 font-semibold" wire:navigate>
        {{ __('Login') }}
    </flux:button>

    {{-- Mobile Menu --}}
    <flux:dropdown class="lg:hidden">
        <flux:button icon="bars-2" variant="ghost" aria-label="Open menu" />
        <flux:menu class="w-[95vw] sm:w-80 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-xl">
            <flux:navlist.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
                {{ __('Home') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('about')" :current="request()->routeIs('about')" wire:navigate>
                {{ __('About') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('product')" :current="request()->routeIs('product')" wire:navigate>
                {{ __('Product') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('opportunity')" :current="request()->routeIs('opportunity')" wire:navigate>
                {{ __('Opportunity') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('edukasi')" :current="request()->routeIs('edukasi')" wire:navigate>
                {{ __('Edukasi') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('contact')" :current="request()->routeIs('contact')" wire:navigate>
                {{ __('Contact') }}
            </flux:navlist.item>
            <div class="p-3 border-t border-zinc-100 dark:border-zinc-700">
                <flux:button href="{{ route('login') }}" variant="primary"
                    class="w-full rounded-full font-semibold" wire:navigate>
                    {{ __('Login') }}
                </flux:button>
            </div>
        </flux:menu>
    </flux:dropdown>

</flux:header>