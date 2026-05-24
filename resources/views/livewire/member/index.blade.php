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

    <flux:modal name="member-form-modal" class="max-w-3xl md:min-w-3xl" wire:model="showModal"
        @close="$wire.closeModal()">
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
                    <flux:input wire:model.live="sponsorUsername" :label="__('Sponsor Username')" type="text"
                        placeholder="{{ __('Optional') }}" />
                    <flux:input :label="__('Sponsor Name')" type="text" :value="$sponsorName" readonly />

                    <flux:input wire:model.live="parentUsername" :label="__('Parent Username')" type="text"
                        placeholder="{{ __('Optional') }}" />
                    <flux:input :label="__('Parent Name')" type="text" :value="$parentName" readonly />

                    <flux:input wire:model="form.username" :label="__('Username')" placeholder="johndoe" />


                    <flux:input wire:model="form.name" :label="__('Name')" placeholder="John Doe" />

                    <flux:input wire:model="form.email" type="email" :label="__('Email')"
                        placeholder="john@example.com" />

                    <flux:select wire:model.live="profile.country_id" :label="__('Country')">
                        <flux:select.option value="">{{ __('Select a country') }}</flux:select.option>
                        @foreach($countries as $id => $country)
                        <flux:select.option value="{{ $id }}">{{ $country }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="profile.phone" :label="__('Phone')" />

                    <flux:input wire:model="profile.birth_date" type="date" :label="__('Birth Date')" />

                    <flux:select wire:model="profile.gender" :label="__('Gender')">
                        <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                        <flux:select.option value="male">{{ __('Male') }}</flux:select.option>
                        <flux:select.option value="female">{{ __('Female') }}</flux:select.option>
                    </flux:select>

                    <flux:input wire:model="profile.id_card_number" type="number" :label="__('KTP')" />

                    <flux:input wire:model="profile.npwp_number" type="number" :label="__('NPWP')" />

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