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
                    <flux:input wire:model="form.name" :label="__('Name')" placeholder="John Doe" />
                    <flux:input wire:model="form.username" :label="__('Username')" placeholder="johndoe" />
                    <flux:input wire:model="form.email" type="email" :label="__('Email')"
                        placeholder="john@example.com" />
                    <flux:select wire:model="form.status" :label="__('Status')">
                        <flux:select.option value="active">active</flux:select.option>
                        <flux:select.option value="suspended">suspended</flux:select.option>
                        <flux:select.option value="inactive">inactive</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="form.referral_code" :label="__('Referral Code')" placeholder="REF001" />
                    <flux:input wire:model="form.email_verified_at" type="datetime-local"
                        :label="__('Email Verified At')" />
                    <flux:input wire:model="form.last_login_at" type="datetime-local" :label="__('Last Login At')" />
                    <flux:input wire:model="form.password" type="password" :label="__('Password')" />
                    <flux:input wire:model="form.password_confirmation" type="password"
                        :label="__('Password Confirmation')" />
                </div>

                <hr class="my-4" />

                <div>
                    <flux:heading size="md">{{ __('Profile (optional)') }}</flux:heading>
                    <div class="grid gap-4 md:grid-cols-2 mt-3">
                        <flux:select wire:model="profile.gender" :label="__('Gender')">
                            <flux:select.option value="">{{ __('Select') }}</flux:select.option>
                            <flux:select.option value="male">{{ __('Male') }}</flux:select.option>
                            <flux:select.option value="female">{{ __('Female') }}</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="profile.birth_date" type="date" :label="__('Birth Date')" />
                        <flux:input wire:model="profile.phone" :label="__('Phone')" />
                        <flux:input wire:model="profile.address" :label="__('Address')" />
                        <flux:select wire:model="profile.country_id" :label="__('Country')">
                            <flux:select.option value="">{{ __('Select a country') }}</flux:select.option>
                            @foreach($countries as $id => $country)
                            <flux:select.option value="{{ $id }}">{{ $country }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="profile.province_id" :label="__('Province')">
                            <flux:select.option value="">{{ __('Select a province') }}</flux:select.option>
                            @foreach($provinces as $id => $province)
                            <flux:select.option value="{{ $id }}">{{ $province }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="profile.city_id" :label="__('City')">
                            <flux:select.option value="">{{ __('Select a city') }}</flux:select.option>
                            @foreach($cities as $id => $city)
                            <flux:select.option value="{{ $id }}">{{ $city }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="profile.district_id" :label="__('District')">
                            <flux:select.option value="">{{ __('Select a district') }}</flux:select.option>
                            @foreach($districts as $id => $district)
                            <flux:select.option value="{{ $id }}">{{ $district }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="profile.village_id" :label="__('Village')">
                            <flux:select.option value="">{{ __('Select a village') }}</flux:select.option>
                            @foreach($villages as $id => $village)
                            <flux:select.option value="{{ $id }}">{{ $village }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <hr class="my-4" />

                <div>
                    <flux:heading size="md">{{ __('Network (optional)') }}</flux:heading>
                    <div class="grid gap-4 md:grid-cols-2 mt-3">
                                <flux:input wire:model.live="sponsorUsername" :label="__('Sponsor Username')" type="text"
                            placeholder="{{ __('Optional') }}" />
                        <flux:input :label="__('Sponsor Name')" type="text" :value="$sponsorName" readonly />

                        <flux:input wire:model.live="parentUsername" :label="__('Parent Username')" type="text"
                            placeholder="{{ __('Optional') }}" />
                        <flux:input :label="__('Parent Name')" type="text" :value="$parentName" readonly />

                        <flux:select wire:model="network.position" :label="__('Position')">
                            <flux:select.option value="">{{ __('Unassigned') }}</flux:select.option>
                            <flux:select.option value="left">{{ __('Left') }}</flux:select.option>
                            <flux:select.option value="right">{{ __('Right') }}</flux:select.option>
                        </flux:select>

                        <flux:input wire:model="network.rank" type="number" :label="__('Rank')" />
                        <flux:input wire:model="network.group" type="number" :label="__('Group')" />
                    </div>
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