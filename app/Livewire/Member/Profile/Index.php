<?php

namespace App\Livewire\Member\Profile;

use App\Actions\Member\Profile\CreateProfileAction;
use App\Actions\Member\Profile\UpdateProfileAction;
use App\Data\MemberProfileData;
use App\Http\Requests\Member\Profile\StoreProfileRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Member;
use App\Models\MemberProfile;
use App\Models\Province;
use App\Models\Village;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Member Profile')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showModal = false;

    public ?int $editingProfileId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function create(): void
    {
        $this->authorize('create', MemberProfile::class);

        $this->editingProfileId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('profile:edit')]
    public function edit(int $profileId): void
    {
        $profile = MemberProfile::query()->findOrFail($profileId);
        $this->authorize('update', $profile);

        $this->editingProfileId = $profile->id;
        $this->form = [
            'member_id' => $profile->member_id,
            'gender' => $profile->gender,
            'birth_date' => $profile->birth_date?->format('Y-m-d'),
            'phone' => $profile->phone,
            'profile_photo' => $profile->profile_photo,
            'country_id' => $profile->country_id,
            'province_id' => $profile->province_id,
            'city_id' => $profile->city_id,
            'district_id' => $profile->district_id,
            'village_id' => $profile->village_id,
            'address' => $profile->address,
        ];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $profile = $this->editingProfileId
            ? MemberProfile::query()->findOrFail($this->editingProfileId)
            : null;

        if ($profile) {
            $this->authorize('update', $profile);
        } else {
            $this->authorize('create', MemberProfile::class);
        }

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($profile)],
            [],
            $this->prefixedAttributes()
        );

        $profileData = MemberProfileData::fromArray($validated['form']);

        if ($profile) {
            UpdateProfileAction::run($profile, $profileData);
            Flux::toast(variant: 'success', text: 'Profile updated successfully.');
        } else {
            CreateProfileAction::run($profileData);
            Flux::toast(variant: 'success', text: 'Profile created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-profileTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $members = Member::query()->orderBy('name')->get();
        $countries = Country::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $provinces = Province::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $cities = City::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $districts = District::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $villages = Village::query()->orderBy('name')->pluck('name', 'id')->toArray();

        return view('livewire.member.profile.index', compact('members', 'countries', 'provinces', 'cities', 'districts', 'villages'))
            ->layout('layouts.app', ['title' => __('Member Profile')]);
    }

    private function prefixedRules(?MemberProfile $profile = null): array
    {
        $rules = $this->rules($profile);

        $prefixed = [];
        foreach ($rules as $key => $rule) {
            $prefixed["form.{$key}"] = $rule;
        }

        return $prefixed;
    }

    private function prefixedAttributes(): array
    {
        $attributes = $this->attributes();

        $prefixed = [];
        foreach ($attributes as $key => $label) {
            $prefixed["form.{$key}"] = $label;
        }

        return $prefixed;
    }

    public function rules(?MemberProfile $profile = null): array
    {
        return StoreProfileRequest::rules($profile);
    }

    public function attributes(): array
    {
        return StoreProfileRequest::attributeLabels();
    }

    private function resetForm(): void
    {
        $this->form = [
            'member_id' => null,
            'gender' => null,
            'birth_date' => null,
            'phone' => null,
            'profile_photo' => null,
            'country_id' => null,
            'province_id' => null,
            'city_id' => null,
            'district_id' => null,
            'village_id' => null,
            'address' => null,
        ];
    }
}
