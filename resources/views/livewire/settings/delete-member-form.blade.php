<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Delete member account') }}</flux:heading>
        <flux:subheading>{{ __('Delete your member account and all of its resources') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-member-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-member-deletion')">
            {{ __('Delete member account') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-member-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form method="POST" wire:submit="deleteMember" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Are you sure you want to delete your member account?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Once your member account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your member account.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Password')" type="password" viewable />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Delete member account') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
