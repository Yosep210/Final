<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Country') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage country master data for the application.') }}
            </flux:text>
        </div>

        <flux:button wire:click="create" variant="primary">
            {{ __('Add Country') }}
        </flux:button>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:country.country-table />
    </div>

    <flux:modal
        name="country-form-modal"
        class="max-w-2xl md:min-w-2xl"
        wire:model="showModal"
        @close="$wire.closeModal()"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingCountryId ? __('Edit Country') : __('Add Country') }}
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Fill in the country information below.') }}
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model="form.iso" :label="__('ISO')" placeholder="ID" />
                    <flux:input wire:model="form.iso3" :label="__('ISO3')" placeholder="IDN" />
                    <flux:input wire:model="form.name" :label="__('Name')" placeholder="Indonesia" />
                    <flux:input wire:model="form.nice_name" :label="__('Nice Name')" placeholder="Indonesia" />
                    <flux:input wire:model="form.numcode" type="number" :label="__('Numcode')" placeholder="360" />
                    <flux:input wire:model="form.phonecode" type="number" :label="__('Phonecode')" placeholder="62" />
                </div>

                <div class="flex items-center gap-3">
                    <flux:checkbox wire:model="form.status" />
                    <flux:label>{{ __('Active') }}</flux:label>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button type="button" variant="ghost" wire:click="closeModal">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingCountryId ? __('Update') : __('Save') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
