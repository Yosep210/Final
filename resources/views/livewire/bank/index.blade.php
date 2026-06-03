<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Bank') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage bank master data for the application.') }}
            </flux:text>
        </div>

        <flux:button wire:click="create" variant="primary">
            {{ __('Add Bank') }}
        </flux:button>
    </div>

    <livewire:bank.bank-table />

    <flux:modal name="bank-form-modal" class="max-w-2xl md:min-w-2xl" wire:model="showModal"
        @close="$wire.closeModal()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingBankId ? __('Edit Bank') : __('Add Bank') }}
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Fill in the bank information below.') }}
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model="form.name" :label="__('Name')" placeholder="BANK MANDIRI" />
                    <flux:input wire:model="form.code" :label="__('Code')" placeholder="008" />
                    <flux:input wire:model="form.type" :label="__('Type')" placeholder="bank" />
                    <flux:input wire:model="form.flipcode" :label="__('Flip Code')" placeholder="mandiri" />
                    <flux:input wire:model="form.espaycode" :label="__('Espay Code')" placeholder="MANDIRI" />
                    <flux:input wire:model="form.linkitacode" :label="__('Linkita Code')" placeholder="008" />
                    <flux:input wire:model="form.logo" :label="__('Logo URL')" placeholder="logo.png" />
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button type="button" variant="ghost" wire:click="closeModal">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingBankId ? __('Update') : __('Save') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
