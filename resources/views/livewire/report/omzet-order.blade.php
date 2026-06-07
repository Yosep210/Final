<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Laporan Omzet Order') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Laporan data omzet orderan ke perusahaan, memisahkan omzet Generate (Admin) dan Omzet Member.') }}
            </flux:text>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-zinc-200 dark:border-zinc-800">
        <button 
            type="button" 
            wire:click="setTab('daily')" 
            class="py-2.5 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $tab === 'daily' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400' }}"
        >
            Harian
        </button>
        <button 
            type="button" 
            wire:click="setTab('monthly')" 
            class="py-2.5 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $tab === 'monthly' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400' }}"
        >
            Bulanan
        </button>
    </div>

    <!-- Filters based on Tab -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
        @if($tab === 'daily')
            <div>
                <flux:input type="date" label="Tanggal Mulai" wire:model.live="startDate" />
            </div>
            <div>
                <flux:input type="date" label="Tanggal Selesai" wire:model.live="endDate" />
            </div>
        @else
            <div>
                <flux:input type="month" label="Bulan Mulai" wire:model.live="startMonth" />
            </div>
            <div>
                <flux:input type="month" label="Bulan Selesai" wire:model.live="endMonth" />
            </div>
        @endif
        <div class="flex items-end justify-start sm:col-span-2">
            <flux:button variant="ghost" class="w-full sm:w-auto" wire:click="resetFilters">
                Reset Filter
            </flux:button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-medium bg-zinc-50 dark:bg-zinc-800/20">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">
                            {{ $tab === 'daily' ? 'Tanggal' : 'Bulan' }}
                        </th>
                        <th class="py-3 px-4 text-right">Omzet Generate</th>
                        <th class="py-3 px-4 text-right">Omzet Member</th>
                        <th class="py-3 px-4 text-right text-indigo-600 dark:text-indigo-400">Total Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($orders as $index => $row)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/10">
                            <td class="py-3 px-4">{{ $orders->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-medium text-zinc-900 dark:text-zinc-100">
                                @if($tab === 'daily')
                                    {{ \Carbon\Carbon::parse($row->label)->locale('id')->isoFormat('DD MMM YYYY') }}
                                @else
                                    {{ \Carbon\Carbon::parse($row->label . '-01')->locale('id')->isoFormat('MMMM YYYY') }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-700 dark:text-zinc-300 font-mono">
                                Rp {{ number_format($row->omzet_generate, 0) }}
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-700 dark:text-zinc-300 font-mono">
                                Rp {{ number_format($row->omzet_order, 0) }}
                            </td>
                            <td class="py-3 px-4 text-right text-indigo-600 dark:text-indigo-400 font-semibold font-mono bg-zinc-50/30 dark:bg-zinc-800/5">
                                Rp {{ number_format($row->total_omzet, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500">
                                {{ __('Tidak ada data omzet order ditemukan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
