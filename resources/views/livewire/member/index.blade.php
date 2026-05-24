<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Member') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage member data for the application.') }}
            </flux:text>
        </div>

        @if ($canManageMembers)
            <flux:button wire:click="create" variant="primary">
                {{ __('Add Member') }}
            </flux:button>
        @endif
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        <livewire:member.member-table />
    </div>

    @if ($canManageMembers)
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

                        <flux:select wire:key="member-country-{{ $showModal ? 'open' : 'closed' }}-{{ $profile['country_id'] ?? 'none' }}" wire:model.live="profile.country_id" :label="__('Country')">
                            <flux:select.option value="" disabled>{{ __('Select a country') }}</flux:select.option>
                            @foreach($countries as $id => $country)
                            <flux:select.option value="{{ $id }}">{{ $country }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Phone/WhatsApp') }}</label>
                            <div class="flex gap-2">
                                <div class="w-24 flex items-center px-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-800">
                                    <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $phoneCode ?? '+' }}</span>
                                </div>
                                <input type="tel" wire:model="phoneNumber" placeholder="8XX XXXX XXXX"
                                    class="flex-1 px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg dark:bg-zinc-800 dark:text-white" />
                            </div>
                        </div>

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
    @endif
</div>
