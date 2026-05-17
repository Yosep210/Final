<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Member') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage member data for the application.') }}
            </flux:text>
        </div>

        <flux:button wire:click="create" variant="primary">
            {{ __('Add Member') }}
        </flux:button>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:member.member-table />
    </div>

    <flux:modal name="member-form-modal" class="max-w-3xl md:min-w-3xl" wire:model="showModal" @close="$wire.closeModal()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingMemberId ? __('Edit Member') : __('Add Member') }}
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Fill in the member information below.') }}
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input wire:model="form.name" :label="__('Name')" placeholder="John Doe" />
                    <flux:input wire:model="form.username" :label="__('Username')" placeholder="johndoe" />
                    <flux:input wire:model="form.email" type="email" :label="__('Email')" placeholder="john@example.com" />
                    <flux:select wire:model="form.status" :label="__('Status')">
                        <flux:select.option value="active">active</flux:select.option>
                        <flux:select.option value="suspended">suspended</flux:select.option>
                        <flux:select.option value="inactive">inactive</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="form.referral_code" :label="__('Referral Code')" placeholder="REF001" />
                    <flux:input wire:model="form.email_verified_at" type="datetime-local" :label="__('Email Verified At')" />
                    <flux:input wire:model="form.last_login_at" type="datetime-local" :label="__('Last Login At')" />
                    <flux:input wire:model="form.password" type="password" :label="__('Password')" />
                    <flux:input wire:model="form.password_confirmation" type="password" :label="__('Password Confirmation')" />
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:button variant="ghost" wire:click="closeModal">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Save') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
