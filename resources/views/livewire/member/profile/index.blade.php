<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Member Profile') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage member data for the application.') }}
            </flux:text>
        </div>

        <flux:button wire:click="create" variant="primary">
            {{ __('Add Member Profile') }}
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
                    {{ $editingMemberId ? __('Edit Member Profile') : __('Add Member Profile') }}
                </flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Fill in the member profile information below.') }}
                </flux:text>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:select wire:model="form.member_id" :label="__('Member')">
                        <flux:select.option value="" disabled>{{ __('Select a member') }}</flux:select.option>
                        @foreach($members as $member)
                        <flux:select.option value="{{ $member->id }}">{{ $member->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="form.date_of_birth" type="date" :label="__('Date of Birth')" />
                    <flux:select wire:model="form.gender" :label="__('Gender')">
                        <flux:select.option value="male">{{ __('Male') }}</flux:select.option>
                        <flux:select.option value="female">{{ __('Female') }}</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="form.country" :label="__('Country')">
                        <flux:select.option value="" disabled>{{ __('Select a country') }}</flux:select.option>
                        @foreach($countries as $country)
                        <flux:select.option value="{{ $country }}">{{ $country }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="form.province" :label="__('Province')">
                        <flux:select.option value="" disabled>{{ __('Select a province') }}</flux:select.option>
                        @foreach($provinces as $province)
                        <flux:select.option value="{{ $province }}">{{ $province }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="form.city" :label="__('City')">
                        <flux:select.option value="" disabled>{{ __('Select a city') }}</flux:select.option>
                        @foreach($cities as $city)
                        <flux:select.option value="{{ $city }}">{{ $city }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="form.district" :label="__('District')">
                        <flux:select.option value="" disabled>{{ __('Select a district') }}</flux:select.option>
                        @foreach($districts as $district)
                        <flux:select.option value="{{ $district }}">{{ $district }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="form.village" :label="__('Village')">
                        <flux:select.option value="" disabled>{{ __('Select a village') }}</flux:select.option>
                        @foreach($villages as $village)
                        <flux:select.option value="{{ $village }}">{{ $village }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="form.address" :label="__('Address')" placeholder="123 Main St" />
                    <flux:input wire:model="form.phone_number" :label="__('Phone Number')"
                        placeholder="+1 555-123-4567" />
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