<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Laporan Pairing Qualified') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Laporan data kualifikasi pairing volume kanan dan kiri member.') }}
            </flux:text>
        </div>
    </div>

    @if($isAdmin)
        <!-- Admin List View -->
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <livewire:report.pairing-table />
        </div>

        <!-- Detail Modal for Admin -->
        <flux:modal name="pairing-detail-modal" class="max-w-4xl md:min-w-4xl" wire:model="showDetailModal" @close="$wire.closeDetail()">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Detail Pairing') }} - {{ $selectedMember?->name }} ({{ strtoupper($selectedMember?->username ?? '') }})
                    </flux:heading>
                    <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                        {{ __('Histori volume harian dan kecocokan (pairing) yang dicapai.') }}
                    </flux:text>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="text-xs text-zinc-500 font-medium uppercase">Total Volume Kiri</div>
                        <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($totalLeft, 0) }} BV
                        </div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="text-xs text-zinc-500 font-medium uppercase">Total Volume Kanan</div>
                        <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">
                            {{ number_format($totalRight, 0) }} BV
                        </div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <div class="text-xs text-zinc-500 font-medium uppercase">Total Pairing Matched</div>
                        <div class="text-xl font-bold font-mono text-indigo-600 dark:text-indigo-400 mt-1">
                            {{ number_format($totalMatched, 0) }} BV
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500 font-medium">
                                <th class="py-2 px-3">#</th>
                                <th class="py-2 px-3">Tanggal</th>
                                <th class="py-2 px-3 text-right">Volume Kiri</th>
                                <th class="py-2 px-3 text-right">Volume Kanan</th>
                                <th class="py-2 px-3 text-right text-indigo-600 dark:text-indigo-400 font-semibold">Matched</th>
                                <th class="py-2 px-3 text-right">Bonus (Gross)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-mono">
                            @forelse($memberLogs as $index => $log)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                                    <td class="py-2 px-3 text-zinc-500 font-sans">{{ $index + 1 }}</td>
                                    <td class="py-2 px-3 text-zinc-500 text-xs font-sans">
                                        {{ $log->created_at?->locale('id')->isoFormat('DD MMM YYYY HH:mm') ?? '-' }}
                                    </td>
                                    <td class="py-2 px-3 text-right">{{ number_format($log->left_volume, 0) }} BV</td>
                                    <td class="py-2 px-3 text-right">{{ number_format($log->right_volume, 0) }} BV</td>
                                    <td class="py-2 px-3 text-right text-indigo-600 dark:text-indigo-400 font-semibold">{{ number_format($log->matched_volume, 0) }} BV</td>
                                    <td class="py-2 px-3 text-right">Rp {{ number_format($log->gross_commission, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-zinc-500 font-sans">
                                        {{ __('Tidak ada histori pairing untuk member ini.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <flux:button variant="ghost" wire:click="closeDetail">{{ __('Tutup') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @else
        <!-- Member Detail Inline View -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="text-xs text-zinc-500 font-medium uppercase">Total Volume Kiri</div>
                    <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">
                        {{ number_format($totalLeft, 0) }} BV
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="text-xs text-zinc-500 font-medium uppercase">Total Volume Kanan</div>
                    <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">
                        {{ number_format($totalRight, 0) }} BV
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="text-xs text-zinc-500 font-medium uppercase">Total Pairing Matched</div>
                    <div class="text-xl font-bold font-mono text-indigo-600 dark:text-indigo-400 mt-1">
                        {{ number_format($totalMatched, 0) }} BV
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500 font-medium">
                            <th class="py-2 px-3">#</th>
                            <th class="py-2 px-3">Tanggal</th>
                            <th class="py-2 px-3 text-right">Volume Kiri</th>
                            <th class="py-2 px-3 text-right">Volume Kanan</th>
                            <th class="py-2 px-3 text-right text-indigo-600 dark:text-indigo-400 font-semibold">Matched</th>
                            <th class="py-2 px-3 text-right">Bonus (Gross)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-mono">
                        @forelse($memberLogs as $index => $log)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                                <td class="py-2 px-3 text-zinc-500 font-sans">{{ $index + 1 }}</td>
                                <td class="py-2 px-3 text-zinc-500 text-xs font-sans">
                                    {{ $log->created_at?->locale('id')->isoFormat('DD MMM YYYY HH:mm') ?? '-' }}
                                </td>
                                <td class="py-2 px-3 text-right">{{ number_format($log->left_volume, 0) }} BV</td>
                                <td class="py-2 px-3 text-right">{{ number_format($log->right_volume, 0) }} BV</td>
                                <td class="py-2 px-3 text-right text-indigo-600 dark:text-indigo-400 font-semibold">{{ number_format($log->matched_volume, 0) }} BV</td>
                                <td class="py-2 px-3 text-right">Rp {{ number_format($log->gross_commission, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-500 font-sans">
                                    {{ __('Tidak ada histori pairing ditemukan.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
