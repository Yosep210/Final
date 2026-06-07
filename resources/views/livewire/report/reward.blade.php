<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Laporan Reward') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Laporan pencapaian reward member berdasarkan akumulasi volume jaringan kanan dan kiri (1 Poin = 1,000 BV).') }}
            </flux:text>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-zinc-200 dark:border-zinc-800">
        <button 
            type="button" 
            wire:click="setTab('achievements')" 
            class="py-2.5 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $activeTab === 'achievements' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400' }}"
        >
            Pencapaian Member
        </button>
        <button 
            type="button" 
            wire:click="setTab('configs')" 
            class="py-2.5 px-4 font-medium text-sm border-b-2 transition-colors duration-200 {{ $activeTab === 'configs' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400' }}"
        >
            Daftar Reward
        </button>
    </div>

    @if($activeTab === 'achievements')
        <!-- Filters -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
            <div>
                <flux:input label="Username" placeholder="Cari username..." wire:model.live="username" />
            </div>
            <div>
                <flux:input label="Nama" placeholder="Cari nama member..." wire:model.live="name" />
            </div>
            <div class="flex items-end justify-start sm:col-span-2">
                <flux:button variant="ghost" class="w-full sm:w-auto" wire:click="resetFilters">
                    Reset Filter
                </flux:button>
            </div>
        </div>

        <!-- Data Table Achievements -->
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-medium bg-zinc-50 dark:bg-zinc-800/20">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Member</th>
                            <th class="py-3 px-4 text-center">Poin Kiri</th>
                            <th class="py-3 px-4 text-center">Poin Kanan</th>
                            <th class="py-3 px-4 text-center">Kualifikasi Poin</th>
                            <th class="py-3 px-4">Reward Yang Dicapai</th>
                            <th class="py-3 px-4">Target Reward Berikutnya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($achievements as $index => $row)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/10">
                                <td class="py-3 px-4">{{ $achievements->firstItem() + $index }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ strtoupper($row->username) }}</div>
                                    <div class="text-xs text-zinc-500">{{ strtoupper($row->name) }}</div>
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ number_format($row->leftPoints) }} <span class="text-[10px] text-zinc-500 font-sans">({{ number_format($row->left_volume, 0) }} BV)</span>
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ number_format($row->rightPoints) }} <span class="text-[10px] text-zinc-500 font-sans">({{ number_format($row->right_volume, 0) }} BV)</span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold font-mono text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($row->points) }}
                                </td>
                                <td class="py-3 px-4">
                                    @if(count($row->achieved_list) > 0)
                                        <div class="flex flex-col gap-1">
                                            @foreach($row->achieved_list as $rName)
                                                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10 dark:bg-emerald-950/20 dark:text-emerald-400 dark:ring-emerald-500/20">
                                                    ✓ {{ $rName }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-zinc-400 text-xs italic">- Belum ada -</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($row->next_reward)
                                        <div class="text-xs">
                                            <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $row->next_reward->reward }}</div>
                                            <div class="text-zinc-500 mt-1">
                                                Butuh <span class="font-bold font-mono text-zinc-800 dark:text-zinc-200">{{ number_format($row->next_reward->req_points) }}</span> poin (Kurang <span class="text-rose-500 font-bold font-mono">{{ number_format($row->next_reward->req_points - $row->points) }}</span> poin)
                                            </div>
                                            <!-- Progress bar -->
                                            @php
                                                $pct = ($row->points / $row->next_reward->req_points) * 100;
                                                $pct = min(100, max(0, $pct));
                                            @endphp
                                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                                <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-amber-600 dark:text-amber-400 text-xs font-bold font-sans">🎉 Semua Reward Telah Dicapai!</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-zinc-500">
                                    {{ __('Tidak ada data pencapaian member ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $achievements->links() }}
            </div>
        </div>
    @else
        <!-- Data Table Configs -->
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800 text-zinc-500 font-medium bg-zinc-50 dark:bg-zinc-800/20">
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Nama Reward</th>
                            <th class="py-3 px-4 text-center">Poin Syarat (Kanan & Kiri)</th>
                            <th class="py-3 px-4 text-right">Nilai Cash (Nominal)</th>
                            <th class="py-3 px-4 text-center">Gelar Pangkat</th>
                            <th class="py-3 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($rewardConfigs as $index => $row)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/10">
                                <td class="py-3 px-4">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $row->reward }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($row->point) }} Poin
                                    <span class="block text-[10px] text-zinc-500 font-sans">({{ number_format($row->point * 1000) }} BV)</span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-zinc-700 dark:text-zinc-300">
                                    Rp {{ number_format($row->nominal, 0) }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-950/20 dark:text-indigo-400 dark:ring-indigo-500/30">
                                        ★ {{ $row->rank ?: '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-zinc-500">
                                    {{ $row->message ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-500">
                                    {{ __('Tidak ada konfigurasi reward ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
