<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('eWallet Logs') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Track and audit all member eWallet transactions and balances.') }}
            </flux:text>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:wallet.wallet-table />
    </div>

    <flux:modal name="wallet-detail-modal" class="max-w-5xl md:min-w-5xl" wire:model="showDetailModal"
        @close="$wire.closeDetail()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('eWallet Detail') }} - {{ $selectedMember?->name }} ({{ strtoupper($selectedMember?->username ?? '') }})
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('List of eWallet transactions for this member.') }}
                </flux:text>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500 font-medium">
                            <th class="py-2 px-3">#</th>
                            <th class="py-2 px-3">{{ __('Date') }}</th>
                            <th class="py-2 px-3">{{ __('Type') }}</th>
                            <th class="py-2 px-3">{{ __('Category') }}</th>
                            <th class="py-2 px-3 text-right">{{ __('Nominal') }}</th>
                            <th class="py-2 px-3 text-right">{{ __('Tax') }}</th>
                            <th class="py-2 px-3 text-right">{{ __('Auto RO') }}</th>
                            <th class="py-2 px-3 text-right">{{ __('Net Amount') }}</th>
                            <th class="py-2 px-3">{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($memberWalletLogs as $index => $log)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="py-2.5 px-3">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-3 text-zinc-500 text-xs">
                                    {{ $log->created_at?->locale('id')?->isoFormat('DD MMM YY HH:mm') ?? '-' }}
                                </td>
                                <td class="py-2.5 px-3">
                                    @php
                                        $type = strtoupper($log->type ?? '');
                                        $typeClass = $type === 'IN'
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20'
                                            : 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20';
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $typeClass }}">
                                        {{ $type }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-zinc-600 dark:text-zinc-300">
                                    {{ ucfirst($log->category ?? '-') }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-medium">
                                    Rp {{ number_format($log->nominal, 0) }}
                                </td>
                                <td class="py-2.5 px-3 text-right text-zinc-500 text-xs">
                                    Rp {{ number_format($log->tax, 0) }}
                                </td>
                                <td class="py-2.5 px-3 text-right text-zinc-500 text-xs">
                                    Rp {{ number_format($log->autoro, 0) }}
                                </td>
                                <td class="py-2.5 px-3 text-right {{ $type === 'IN' ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-rose-600 dark:text-rose-400 font-semibold' }}">
                                    Rp {{ number_format($log->amount, 0) }}
                                </td>
                                <td class="py-2.5 px-3 text-zinc-600 dark:text-zinc-300 max-w-xs truncate" title="{{ $log->description }}">
                                    {{ $log->description ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-zinc-500">
                                    {{ __('No eWallet logs found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <flux:button variant="ghost" wire:click="closeDetail">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
