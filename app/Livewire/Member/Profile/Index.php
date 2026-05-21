<?php

namespace App\Livewire\Member\Profile;

use App\Actions\Member\Profile\CreateProfileAction;
use App\Actions\Member\Profile\UpdateProfileAction;
use App\Data\MemberProfileData;
use App\Http\Requests\Member\Profile\StoreProfileRequest;
use App\Models\MemberProfile;
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
        $this->authorize('Update', $profile);

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
            $this->authorize('Update', $profile);
        } else {
            $this->authorize('Create', MemberProfile::class);
        }

        $validated = $this->validate(
            $profile
                ? UpdateProfileRequest::rules($profile)
                : StoreProfileRequest::rules()
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
        return view('livewire.member.profile.index')
            ->extends('layouts.app', ['title' => __('Member Profile')]);
    }

    public function rules(?MemberProfile $profile = null): array
    {
        return $profile
            ? UpdateProfileRequest::rules($profile)
            : StoreProfileRequest::rules();
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
