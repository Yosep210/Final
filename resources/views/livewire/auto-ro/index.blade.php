<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div>
        <flux:heading size="xl">{{ __('Auto RO') }}</flux:heading>
        <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
            {{ __('Ringkasan Auto RO, history transaksi, dan saldo Auto RO.') }}
        </flux:text>
    </div>

    <div
        class="flex flex-wrap gap-2 rounded-xl border border-neutral-200 bg-white p-2 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <button type="button" wire:click="setTab('saldo')"
            class="rounded-lg px-4 py-2 text-sm font-medium {{ $activeTab === 'saldo' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
            {{ __('Saldo Auto RO') }}
        </button>
        <button type="button" wire:click="setTab('monthly')"
            class="rounded-lg px-4 py-2 text-sm font-medium {{ $activeTab === 'monthly' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
            {{ __('Auto RO Bulanan') }}
        </button>
        <button type="button" wire:click="setTab('history')"
            class="rounded-lg px-4 py-2 text-sm font-medium {{ $activeTab === 'history' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
            {{ __('History Auto RO') }}
        </button>
    </div>

    @if ($activeTab === 'saldo')
    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <div class="mb-4">
            <flux:heading size="lg">{{ __('Saldo Auto RO') }}</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Saldo Auto RO per member.') }}</flux:text>
        </div>
        <livewire:auto-ro.auto-ro-table />
    </div>
    @endif

    @if ($activeTab === 'monthly')
    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <div class="mb-4">
            <flux:heading size="lg">{{ __('Auto RO Bulanan') }}</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Ringkasan total Auto RO per bulan.') }}
            </flux:text>
        </div>
        <livewire:auto-ro.monthly-table />
    </div>
    @endif

    @if ($activeTab === 'history')
    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <div class="mb-4">
            <flux:heading size="lg">{{ __('History Auto RO') }}</flux:heading>
            <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Daftar transaksi Auto RO terbaru.') }}
            </flux:text>
        </div>
        <livewire:auto-ro.history-table />
    </div>
    @endif
</div>