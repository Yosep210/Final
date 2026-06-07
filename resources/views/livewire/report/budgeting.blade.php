<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Laporan Budgeting') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Analisis perbandingan alokasi budget (dari total BV) terhadap total pengeluaran bonus real per jenis komisi.') }}
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
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <!-- Main Header Row -->
                    <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-semibold bg-zinc-50 dark:bg-zinc-800/20">
                        <th rowspan="2" class="py-3 px-3 text-center border-r border-zinc-200 dark:border-zinc-850">#</th>
                        <th rowspan="2" class="py-3 px-3 text-left border-r border-zinc-200 dark:border-zinc-850">Bulan</th>
                        <th colspan="2" class="py-2 px-3 text-center border-r border-zinc-200 dark:border-zinc-850">Omzet / BV</th>
                        <th rowspan="2" class="py-3 px-3 text-right bg-zinc-100/50 dark:bg-zinc-800/50 border-r border-zinc-200 dark:border-zinc-850 font-bold text-zinc-900 dark:text-zinc-100">Total Omzet<br>Total BV</th>
                        
                        @foreach($cfg_budget as $type => $conf)
                            <th colspan="3" class="py-2 px-3 text-center border-r border-zinc-200 dark:border-zinc-805 {{ $conf['color'] }} font-bold">
                                {{ $conf['label'] }} ({{ $conf['percent'] }}%)
                            </th>
                        @endforeach

                        <th colspan="2" class="py-2 px-3 text-center bg-zinc-800 text-white dark:bg-zinc-950 font-bold">Total Saldo</th>
                    </tr>
                    <!-- Sub-header Row -->
                    <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-medium bg-zinc-50 dark:bg-zinc-800/10">
                        <th class="py-2 px-3 text-right">Register</th>
                        <th class="py-2 px-3 text-right border-r border-zinc-200 dark:border-zinc-850">Repeat Order</th>

                        @foreach($cfg_budget as $type => $conf)
                            <th class="py-2 px-2 text-right bg-zinc-50/50 dark:bg-zinc-800/10">Budget</th>
                            <th class="py-2 px-2 text-right bg-zinc-50/50 dark:bg-zinc-800/10">Real</th>
                            <th class="py-2 px-2 text-right border-r border-zinc-200 dark:border-zinc-805 bg-zinc-50/80 dark:bg-zinc-800/20 font-semibold">Saldo</th>
                        @endforeach

                        <th class="py-2 px-3 text-right bg-zinc-800 text-white dark:bg-zinc-950">Nominal</th>
                        <th class="py-2 px-3 text-center bg-zinc-800 text-white dark:bg-zinc-950">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-mono">
                    @forelse($orders as $index => $row)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/10">
                            <td class="py-3 px-3 text-center border-r border-zinc-200 dark:border-zinc-850 text-zinc-500 font-sans">
                                {{ $orders->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-zinc-900 dark:text-zinc-100 border-r border-zinc-200 dark:border-zinc-850 font-sans">
                                {{ \Carbon\Carbon::parse($row->period . '-01')->locale('id')->isoFormat('MMM YYYY') }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="text-zinc-900 dark:text-zinc-100 font-medium">Rp{{ number_format($row->omzet_reg, 0) }}</div>
                                <div class="text-[10px] text-zinc-500 font-sans">{{ number_format($row->bv_reg, 0) }} BV</div>
                            </td>
                            <td class="py-3 px-3 text-right border-r border-zinc-200 dark:border-zinc-850">
                                <div class="text-zinc-900 dark:text-zinc-100 font-medium">Rp{{ number_format($row->omzet_ro, 0) }}</div>
                                <div class="text-[10px] text-zinc-500 font-sans">{{ number_format($row->bv_ro, 0) }} BV</div>
                            </td>
                            <td class="py-3 px-3 text-right bg-zinc-50/30 dark:bg-zinc-800/5 font-semibold border-r border-zinc-200 dark:border-zinc-850">
                                <div class="text-zinc-900 dark:text-zinc-100">Rp{{ number_format($row->total_omzet, 0) }}</div>
                                <div class="text-[10px] text-zinc-600 dark:text-zinc-400 font-sans">{{ number_format($row->total_bv, 0) }} BV</div>
                            </td>

                            <!-- Bonus breakdowns -->
                            @foreach($cfg_budget as $type => $conf)
                                @php
                                    $bData = $row->budget_data[$type] ?? ['budget' => 0, 'bonus' => 0, 'saldo' => 0];
                                    $sColor = $bData['saldo'] < 0 ? 'text-rose-600 dark:text-rose-400 font-bold bg-rose-50/50 dark:bg-rose-950/20' : 'text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50/50 dark:bg-emerald-950/20';
                                @endphp
                                <td class="py-3 px-2 text-right text-zinc-600 dark:text-zinc-400">
                                    Rp{{ number_format($bData['budget'], 0) }}
                                </td>
                                <td class="py-3 px-2 text-right text-zinc-700 dark:text-zinc-300">
                                    Rp{{ number_format($bData['bonus'], 0) }}
                                </td>
                                <td class="py-3 px-2 text-right border-r border-zinc-200 dark:border-zinc-805 {{ $sColor }}">
                                    Rp{{ number_format($bData['saldo'], 0) }}
                                </td>
                            @endforeach

                            <!-- Total Saldo -->
                            <td class="py-3 px-3 text-right font-bold bg-zinc-800 text-zinc-100 dark:bg-zinc-950 dark:text-zinc-200 border-r border-zinc-700 dark:border-zinc-900">
                                Rp{{ number_format($row->total_saldo, 0) }}
                            </td>
                            <td class="py-3 px-3 text-center font-bold bg-zinc-800 text-zinc-100 dark:bg-zinc-950 dark:text-zinc-200 border-none font-sans">
                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-2xs font-semibold {{ $row->percentage < 0 ? 'text-rose-400 bg-rose-950/30' : 'text-emerald-400 bg-emerald-950/30' }}">
                                    {{ number_format($row->percentage, 2) }} %
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="24" class="py-8 text-center text-zinc-500 font-sans">
                                {{ __('Tidak ada data budgeting ditemukan.') }}
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
