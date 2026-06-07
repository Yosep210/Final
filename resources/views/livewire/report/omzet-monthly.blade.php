<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Laporan Omzet Bulanan') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Laporan posting omzet bulanan perusahaan, total BV, total komisi keluar (payout), dan persentase rasio payout.') }}
            </flux:text>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
        <div>
            <flux:input type="month" label="Bulan Mulai" wire:model.live="startMonth" />
        </div>
        <div>
            <flux:input type="month" label="Bulan Selesai" wire:model.live="endMonth" />
        </div>
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
                        <th class="py-3 px-4">Bulan Omzet</th>
                        <th class="py-3 px-4 text-right">Omzet Register</th>
                        <th class="py-3 px-4 text-right">Omzet RO</th>
                        <th class="py-3 px-4 text-right">Total Omzet</th>
                        <th class="py-3 px-4 text-right">Total BV</th>
                        <th class="py-3 px-4 text-right text-rose-600 dark:text-rose-400">Total Payout (Bonus)</th>
                        <th class="py-3 px-4 text-center">Rasio Payout</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($orders as $index => $row)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/10">
                            <td class="py-3 px-4">{{ $orders->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-medium text-zinc-900 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($row->month . '-01')->locale('id')->isoFormat('MMMM YYYY') }}
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-700 dark:text-zinc-300 font-mono">
                                Rp {{ number_format($row->register, 0) }}
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-700 dark:text-zinc-300 font-mono">
                                Rp {{ number_format($row->ro, 0) }}
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-900 dark:text-zinc-100 font-semibold font-mono bg-zinc-50/30 dark:bg-zinc-800/5">
                                Rp {{ number_format($row->total_omzet, 0) }}
                            </td>
                            <td class="py-3 px-4 text-right text-zinc-600 dark:text-zinc-400 font-mono">
                                {{ number_format($row->total_bv, 0) }} BV
                            </td>
                            <td class="py-3 px-4 text-right text-rose-600 dark:text-rose-400 font-semibold font-mono">
                                Rp {{ number_format($row->payout, 0) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $p = $row->percentage;
                                    $color = $p > 60 ? 'text-rose-600 bg-rose-50 dark:bg-rose-950/20' : ($p > 40 ? 'text-amber-600 bg-amber-50 dark:bg-amber-950/20' : 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/20');
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $color }}">
                                    {{ number_format($p, 2) }} %
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-zinc-500">
                                {{ __('Tidak ada data omzet bulanan ditemukan.') }}
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
