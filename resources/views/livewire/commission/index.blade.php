<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Commission List') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('View all member commissions and bonuses logs.') }}
            </flux:text>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:commission.commission-table />
    </div>

    <flux:modal name="commission-detail-modal" class="max-w-4xl md:min-w-4xl" wire:model="showDetailModal"
        @close="$wire.closeDetail()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Bonus Detail') }} - {{ $selectedMember?->name }} ({{ strtoupper($selectedMember?->username ?? '') }})
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('List of commissions and bonuses earned by this member.') }}
                </flux:text>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 text-zinc-500 font-medium">
                            <th class="py-2 px-3">#</th>
                            <th class="py-2 px-3">{{ __('Date') }}</th>
                            <th class="py-2 px-3">{{ __('Type') }}</th>
                            <th class="py-2 px-3 text-right">{{ __('Nominal') }}</th>
                            <th class="py-2 px-3 text-right">{{ __('Tax') }}</th>
                            <th class="py-2 px-3 text-right">{{ __('Net') }}</th>
                            <th class="py-2 px-3 class-center">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($memberCommissions as $index => $log)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="py-2.5 px-3">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-3 text-zinc-500 text-xs">
                                    {{ $log->created_at?->locale('id')?->isoFormat('DD MMM YY HH:mm') ?? '-' }}
                                </td>
                                <td class="py-2.5 px-3">
                                    @php
                                        $class = match ($log->type) {
                                            'sponsor' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                                            'pairing' => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                                            'unilevel' => 'bg-amber-50 text-amber-700 ring-amber-600/10 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
                                            'generation' => 'bg-purple-50 text-purple-700 ring-purple-600/10 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20',
                                            default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $class }}">
                                        {{ ucfirst($log->type) }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-right font-medium">
                                    Rp {{ number_format($log->gross_commission, 0) }}
                                </td>
                                <td class="py-2.5 px-3 text-right text-zinc-500 text-xs">
                                    Rp {{ number_format($log->tax_amount, 0) }}
                                </td>
                                <td class="py-2.5 px-3 text-right text-emerald-600 dark:text-emerald-400 font-semibold">
                                    Rp {{ number_format($log->net_commission, 0) }}
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @php
                                        $statusText = $log->is_paid ? __('Paid') : __('Unpaid');
                                        $statusClass = $log->is_paid
                                            ? 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                                            : 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20';
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-zinc-500">
                                    {{ __('No commissions logged yet.') }}
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
