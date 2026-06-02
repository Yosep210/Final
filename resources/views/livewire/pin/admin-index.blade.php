<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Activation PINs') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage, generate, and track MLM Activation PINs.') }}
            </flux:text>
        </div>

        <flux:button wire:click="openGenerateModal" variant="primary">
            {{ __('Generate PINs') }}
        </flux:button>
    </div>

    <!-- Filters and Search -->
    <div class="grid gap-4 md:grid-cols-3 bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700 p-4 rounded-xl">
        <flux:input wire:model.live.debounce.300ms="searchSerial" placeholder="{{ __('Search Serial Number...') }}" />
        
        <flux:input wire:model.live.debounce.300ms="searchOwner" placeholder="{{ __('Search Owner Username/Name...') }}" />

        <flux:select wire:model.live="filterStatus" placeholder="{{ __('Filter Status') }}">
            <flux:select.option value="all">{{ __('All Status') }}</flux:select.option>
            <flux:select.option value="unused">{{ __('Unused') }}</flux:select.option>
            <flux:select.option value="used">{{ __('Used') }}</flux:select.option>
        </flux:select>
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-zinc-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">ID</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Serial Number</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">PIN Code</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Status</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Owner</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Activated For</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Activated At</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($pins as $pin)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="p-4 align-middle text-zinc-600 dark:text-zinc-400 font-mono">{{ $pin->id }}</td>
                            <td class="p-4 align-middle font-mono font-medium text-zinc-900 dark:text-white">{{ $pin->serial_number }}</td>
                            <td class="p-4 align-middle font-mono text-zinc-600 dark:text-zinc-400">{{ $pin->pin_code }}</td>
                            <td class="p-4 align-middle">
                                @if ($pin->status === 'unused')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-950 dark:text-green-300 border border-green-200 dark:border-green-800">
                                        {{ __('Unused') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                        {{ __('Used') }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 align-middle text-zinc-900 dark:text-white">
                                @if ($pin->owner)
                                    <div class="font-medium">{{ $pin->owner->name }}</div>
                                    <div class="text-xs text-zinc-500 font-mono">{{ $pin->owner->username }}</div>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">-</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle text-zinc-900 dark:text-white">
                                @if ($pin->activatedMember)
                                    <div class="font-medium">{{ $pin->activatedMember->name }}</div>
                                    <div class="text-xs text-zinc-500 font-mono">{{ $pin->activatedMember->username }}</div>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">-</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle text-zinc-500 dark:text-zinc-400">
                                {{ $pin->activated_at ? $pin->activated_at->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td class="p-4 align-middle text-zinc-500 dark:text-zinc-400">
                                {{ $pin->created_at ? $pin->created_at->format('Y-m-d H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No activation PINs found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($pins->hasPages())
            <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
                {{ $pins->links() }}
            </div>
        @endif
    </div>

    <!-- Generate Modal -->
    <flux:modal name="generate-pin-modal" class="max-w-md" wire:model="showGenerateModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Generate PINs') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Create new activation PINs in bulk.') }}
                </flux:text>
            </div>

            <form wire:submit="generate" class="space-y-6">
                <flux:input wire:model="quantity" type="number" min="1" max="1000" :label="__('Quantity')" placeholder="e.g. 10" />

                <div>
                    <flux:input wire:model.live.debounce.300ms="targetUsername" :label="__('Owner Username (Optional)')" placeholder="e.g. member01" />
                    @if ($targetName)
                        <div class="mt-1 text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                            {{ $targetName }}
                        </div>
                    @endif
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button type="button" variant="ghost" wire:click="$set('showGenerateModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Generate') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
