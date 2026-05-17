<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Province') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage province master data for the application.') }}
            </flux:text>
        </div>

        <flux:button wire:click="create" variant="primary">
            {{ __('Add Province') }}
        </flux:button>
    </div>

    <livewire:province.province-table />

    <flux:modal name="province-form-modal" class="max-w-2xl md:min-w-2xl" wire:model="showModal"
        @close="$wire.closeModal()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingProvinceId ? __('Edit Province') : __('Add Province') }}
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Fill in the province information below.') }}
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model="form.name" :label="__('Name')" placeholder="Jawa Barat" />
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button type="button" variant="ghost" wire:click="closeModal">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingProvinceId ? __('Update') : __('Save') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>