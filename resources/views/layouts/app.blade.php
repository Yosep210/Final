<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        <div class="items-center gap-2 mb-2 hidden lg:flex">
            <flux:icon name="calendar" class="size-4 text-zinc-500" />
            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{
                now()->locale('id')->isoFormat('dddd, DD MMM YYYY') }}</span>
        </div>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>