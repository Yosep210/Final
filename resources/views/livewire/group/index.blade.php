<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Group List') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('View member groups organized by parent and position.') }}
            </flux:text>
        </div>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:group.group-table />
    </div>

    <flux:modal name="group-detail-modal" class="max-w-3xl md:min-w-3xl" wire:model="showDetailModal"
        @close="$wire.closeDetail()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Group Detail') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Detailed group placement information.') }}
                </flux:text>
            </div>

            @if ($selectedGroup)
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input :label="__('Member')" :value="$selectedGroup->member?->name" readonly />
                    <flux:input :label="__('Username')" :value="$selectedGroup->member?->username" readonly />
                    <flux:input :label="__('Parent')" :value="$selectedGroup->parent?->name ?? '-'" readonly />
                    <flux:input :label="__('Position')" :value="ucfirst($selectedGroup->position ?? '-')" readonly />
                    <flux:input :label="__('Left Volume')" :value="number_format((float) ($selectedGroup->left_volume ?? 0), 2)" readonly />
                    <flux:input :label="__('Right Volume')" :value="number_format((float) ($selectedGroup->right_volume ?? 0), 2)" readonly />
                    <flux:input :label="__('Generation')" :value="$selectedGroup->generation ?? 0" readonly />
                    <flux:input :label="__('Group')" :value="$selectedGroup->group ?? 0" readonly />
                </div>
            @endif

            <div class="flex justify-end">
                <flux:button variant="ghost" wire:click="closeDetail">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
