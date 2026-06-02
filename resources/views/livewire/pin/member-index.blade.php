<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('My Activation PINs') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('View your activation PINs and transfer them to other members.') }}
            </flux:text>
        </div>

        @if ($availablePins->isNotEmpty())
            <flux:button wire:click="openTransferModal" variant="primary">
                {{ __('Transfer PINs') }}
            </flux:button>
        @endif
    </div>

    <!-- Filters and Search -->
    <div class="grid gap-4 md:grid-cols-2 bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700 p-4 rounded-xl">
        <flux:input wire:model.live.debounce.300ms="searchSerial" placeholder="{{ __('Search Serial Number...') }}" />

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
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Serial Number</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">PIN Code</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Status</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Activated For</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Activated At</th>
                        <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($pins as $pin)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
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
                                @if ($pin->activatedMember)
                                    <div class="font-medium">{{ $pin->activatedMember->name }}</div>
                                    <div class="text-xs text-zinc-500 font-mono">{{ $pin->activatedMember->username }}</div>
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-500">-</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle text-zinc-500 dark:text-zinc-400 font-mono">
                                {{ $pin->activated_at ? $pin->activated_at->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td class="p-4 align-middle text-zinc-500 dark:text-zinc-400 font-mono">
                                {{ $pin->created_at ? $pin->created_at->format('Y-m-d H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                                {{ __('No owned PINs found.') }}
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

    <!-- Transfer Modal -->
    <flux:modal name="transfer-pin-modal" class="max-w-xl" wire:model="showTransferModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Transfer PINs') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Transfer unused PINs to another member.') }}
                </flux:text>
            </div>

            <form wire:submit="transfer" class="space-y-6">
                <div>
                    <flux:input wire:model.live.debounce.300ms="recipientUsername" :label="__('Recipient Username')" placeholder="e.g. downline01" />
                    @if ($recipientName)
                        <div class="mt-1 text-xs @if($recipientId) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif font-medium">
                            {{ $recipientName }}
                        </div>
                    @endif
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        {{ __('Select PINs to Transfer') }} (@if($selectedPinSerials) {{ count($selectedPinSerials) }} selected @else 0 selected @endif)
                    </label>
                    <div class="max-h-60 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 divide-y divide-zinc-155 dark:divide-zinc-800 bg-zinc-50 dark:bg-zinc-800">
                        @forelse ($availablePins as $availablePin)
                            <label class="flex items-center gap-3 py-2 px-1 cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700/50">
                                <input type="checkbox" wire:model="selectedPinSerials" value="{{ $availablePin->serial_number }}" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" />
                                <div class="flex-1 font-mono text-sm text-zinc-900 dark:text-white">
                                    <span class="font-medium">{{ $availablePin->serial_number }}</span>
                                    <span class="text-zinc-500 text-xs ml-2">({{ $availablePin->pin_code }})</span>
                                </div>
                            </label>
                        @empty
                            <div class="text-center text-zinc-500 py-4">
                                {{ __('No unused PINs available for transfer.') }}
                            </div>
                        @endforelse
                    </div>
                    @error('selectedPinSerials')
                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button type="button" variant="ghost" wire:click="$set('showTransferModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" :disabled="!$recipientId || empty($selectedPinSerials)">
                        {{ __('Transfer') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
