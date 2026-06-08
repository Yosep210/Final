<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen overflow-x-hidden bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible
        class="overflow-x-hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="hidden lg:flex" />
        </flux:sidebar.header>

        @php
        $menus = \App\Services\MenuService::get('Menu');
        @endphp

        <flux:sidebar.nav class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto pr-1">
            @foreach ($menus as $menu)
            @if (! empty($menu['sub']))
            <flux:sidebar.group :heading="__($menu['heading'])" icon="{{ $menu['icon'] }}" class="grid" expandable
                :expanded="request()->routeIs(...($menu['route'] ?? []))">
                @foreach ($menu['sub'] as $sub)
                <flux:sidebar.item :href="Route::has($sub['href'] ?? '') ? route($sub['href']) : '#'" :current="Route::has($sub['href'] ?? '') && request()->routeIs($sub['href'])"
                    wire:navigate>
                    {{ __($sub['title']) }}
                </flux:sidebar.item>
                @endforeach
            </flux:sidebar.group>
            @else
            <flux:sidebar.item icon="{{ $menu['icon'] }}" :href="Route::has($menu['href'] ?? '') ? route($menu['href']) : '#'"
                :current="Route::has($menu['href'] ?? '') && request()->routeIs($menu['href'])" wire:navigate>
                {{ __($menu['title']) }}
            </flux:sidebar.item>
            @endif
            @endforeach
        </flux:sidebar.nav>

        <div class="hidden lg:block">
            <x-desktop-member-menu :name="auth()->user()->name" />
        </div>
    </flux:sidebar>

    <!-- Mobile Member Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
