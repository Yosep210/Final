<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Stock Produk Member') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Monitor the total, active, and used product PIN stock for each member and stockist.') }}
            </flux:text>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="grid gap-4 md:grid-cols-3 bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700 p-4 rounded-xl">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search Member Username/Name...') }}" />
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-zinc-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">#</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Username') }}</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Name') }}</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300 text-center">{{ __('Total PIN') }}</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300 text-center">{{ __('Active PIN') }}</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300 text-center">{{ __('Used PIN') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($members as $index => $member)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="p-4 align-middle text-zinc-500 dark:text-zinc-400 font-mono">{{ $members->firstItem() + $index }}</td>
                            <td class="p-4 align-middle font-mono font-bold text-zinc-900 dark:text-white">{{ strtoupper($member->username ?? '') }}</td>
                            <td class="p-4 align-middle text-zinc-900 dark:text-white">{{ $member->name }}</td>
                            <td class="p-4 align-middle text-center font-semibold text-zinc-900 dark:text-white">{{ number_format($member->total_pins) }}</td>
                            <td class="p-4 align-middle text-center text-emerald-600 dark:text-emerald-400 font-semibold">{{ number_format($member->active_pins) }}</td>
                            <td class="p-4 align-middle text-center text-zinc-500 dark:text-zinc-400 font-semibold">{{ number_format($member->used_pins) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No member PIN stock found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($members->hasPages())
            <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
