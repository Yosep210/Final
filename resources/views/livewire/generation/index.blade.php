<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Gen List') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('View generation network list.') }}
            </flux:text>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:generation.gen-table />
    </div>

    <flux:modal name="generation-detail-modal" class="max-w-3xl md:min-w-3xl" wire:model="showDetailModal"
        @close="$wire.closeDetail()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Generation Detail') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Detailed generation network information.') }}
                </flux:text>
            </div>

            @if ($selectedNetwork)
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input :label="__('Member')" :value="$selectedNetwork->member?->name" readonly />
                    <flux:input :label="__('Username')" :value="$selectedNetwork->member?->username" readonly />
                    <flux:input :label="__('Sponsor')" :value="$selectedNetwork->sponsor?->name ?? '-'" readonly />
                    <flux:input :label="__('Parent')" :value="$selectedNetwork->parent?->name ?? '-'" readonly />
                    <flux:input :label="__('Position')" :value="ucfirst($selectedNetwork->position ?? '-')" readonly />
                    <flux:input :label="__('Left Volume')" :value="number_format((float) ($selectedNetwork->left_volume ?? 0), 0)" readonly />
                    <flux:input :label="__('Right Volume')" :value="number_format((float) ($selectedNetwork->right_volume ?? 0), 0)" readonly />
                    <flux:input :label="__('Total Volume')" :value="number_format((float) ($selectedNetwork->total_volume ?? 0), 0)" readonly />
                    <flux:input :label="__('Rank')" :value="ucfirst($selectedNetwork->current_rank ?? 'member')" readonly />
                    <flux:input :label="__('Generation')" :value="$selectedNetwork->generation ?? 0" readonly />
                </div>
            @endif

            <div class="flex justify-end">
                <flux:button variant="ghost" wire:click="closeDetail">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
