<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Role Permission') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage assignments between roles and permissions.') }}
            </flux:text>
        </div>

        <flux:button wire:click="create" variant="primary">
            {{ __('Add Role Permission') }}
        </flux:button>
    </div>

    <livewire:rolepermission.role-permission-table />

    <flux:modal name="role-permission-form-modal" class="max-w-2xl md:min-w-2xl" wire:model="showModal"
        @close="$wire.closeModal()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingRoleId ? __('Edit Role Permission') : __('Add Role Permission') }}
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Assign a permission to a role or update the existing assignment.') }}
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:select wire:model="form.role_id" :label="__('Role')" placeholder="Select role">
                        @foreach ($roles as $id => $role)
                        <flux:select.option value="{{ $id }}">{{ $role }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="form.permission_id" :label="__('Permission')"
                        placeholder="Select permission">
                        @foreach ($permissions as $id => $permission)
                        <flux:select.option value="{{ $id }}">{{ $permission }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:button type="button" variant="ghost" wire:click="closeModal">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        {{ $editingRoleId ? __('Update') : __('Save') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>