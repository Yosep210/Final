<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="grid gap-4 md:grid-cols-2">

                <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />
                <flux:input wire:model="username" :label="__('Username')" type="text" required
                    autocomplete="username" />

                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                <div>
                    <flux:text class="mt-4">
                        {{ __('Your email address is unverified.') }}

                        <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                </div>
                @endif

                <flux:input wire:model="phone" :label="__('Phone / WhatsApp')" type="tel" />
                <flux:input wire:model="id_card_number" :label="__('ID Card Number')" />
                <flux:input wire:model="npwp_number" :label="__('NPWP Number')" />

                <flux:select wire:model="gender" :label="__('Gender')">
                    <flux:select.option value="">{{ __('Select gender') }}</flux:select.option>
                    <flux:select.option value="male">{{ __('Male') }}</flux:select.option>
                    <flux:select.option value="female">{{ __('Female') }}</flux:select.option>
                </flux:select>

                <flux:input wire:model="birth_date" type="date" :label="__('Birth Date')" />

                <flux:select wire:model.live="country_id" :label="__('Country')" wire:key="settings-country-select-{{ $country_id ?? 'none' }}">
                    <flux:select.option value="">{{ __('Select a country') }}</flux:select.option>
                    @foreach($countries as $id => $country)
                    <flux:select.option :value="$id">{{ $country }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="province_id" :label="__('Province')" wire:key="settings-province-select-{{ $country_id ?? 'none' }}-{{ $province_id ?? 'none' }}">
                    <flux:select.option value="">{{ __('Select a province') }}</flux:select.option>
                    @foreach($provinces as $id => $province)
                    <flux:select.option :value="$id">{{ $province }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="city_id" :label="__('City')" wire:key="settings-city-select-{{ $province_id ?? 'none' }}-{{ $city_id ?? 'none' }}">
                    <flux:select.option value="">{{ __('Select a city') }}</flux:select.option>
                    @foreach($cities as $id => $city)
                    <flux:select.option :value="$id">{{ $city }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="district_id" :label="__('District')" wire:key="settings-district-select-{{ $city_id ?? 'none' }}-{{ $district_id ?? 'none' }}">
                    <flux:select.option value="">{{ __('Select a district') }}</flux:select.option>
                    @foreach($districts as $id => $district)
                    <flux:select.option :value="$id">{{ $district }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="village_id" :label="__('Village')" wire:key="settings-village-select-{{ $district_id ?? 'none' }}-{{ $village_id ?? 'none' }}">
                    <flux:select.option value="">{{ __('Select a village') }}</flux:select.option>
                    @foreach($villages as $id => $village)
                    <flux:select.option :value="$id">{{ $village }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="address" :label="__('Address')" />
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>

        @if ($this->showDeleteMember)
        <livewire:settings.delete-member-form />
        @endif
    </x-settings.layout>
</section>