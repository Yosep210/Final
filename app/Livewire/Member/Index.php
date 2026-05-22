<?php

namespace App\Livewire\Member;

use App\Actions\Member\CreateMemberAction;
use App\Actions\Member\Profile\CreateProfileAction;
use App\Actions\Member\UpdateMemberAction;
use App\Data\MemberData;
use App\Data\MemberProfileData;
use App\Http\Requests\Member\Profile\StoreProfileRequest;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Member;
use App\Models\MemberNetwork;
use App\Models\Province;
use App\Models\Village;
use App\Services\MemberNetworkPlacementService;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Member')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showModal = false;

    public ?int $editingMemberId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    /**
     * @var array<string, mixed>
     */
    public array $profile = [];

    /**
     * @var array<string, mixed>
     */
    public array $network = [];

    public string $sponsorUsername = '';

    public ?string $sponsorName = null;

    public string $parentUsername = '';

    public ?string $parentName = null;

    public function mount(): void
    {
        $this->resetForm();
    }

    public function create(): void
    {
        $this->authorize('create', Member::class);

        $this->editingMemberId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('member:edit')]
    public function edit(int $memberId): void
    {
        $member = Member::query()->findOrFail($memberId);
        $this->authorize('Update', $member);

        $this->editingMemberId = $member->id;
        $this->form = [
            'name' => $member->name,
            'username' => $member->username,
            'email' => $member->email,
            'password' => '',
            'password_confirmation' => '',
            'status' => $member->status,
            'referral_code' => $member->referral_code,
            'email_verified_at' => optional($member->email_verified_at)->format('Y-m-d\TH:i'),
            'last_login_at' => optional($member->last_login_at)->format('Y-m-d\TH:i'),
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $member = $this->editingMemberId
            ? Member::query()->findOrFail($this->editingMemberId)
            : null;

        if ($member) {
            $this->authorize('update', $member);
        } else {
            $this->authorize('create', Member::class);
        }

        $validated = $this->validate($this->rules($member), [], $this->attributes());
        $memberData = MemberData::fromArray($validated['form']);

        if ($member) {
            UpdateMemberAction::run($member, $memberData);
            Flux::toast(variant: 'success', text: 'Member updated successfully.');
        } else {
            $created = CreateMemberAction::run($memberData);

            if ($this->hasProfileInput($validated['profile'] ?? [])) {
                $profileArr = $validated['profile'];
                $profileArr['member_id'] = $created->id;
                $profileData = MemberProfileData::fromArray($profileArr);
                CreateProfileAction::run($profileData);
            }

            if ($this->hasNetworkInput($validated['network'] ?? []) || ! empty($validated['sponsorUsername']) || ! empty($validated['parentUsername'])) {
                $networkArr = $validated['network'];
                $networkArr['member_id'] = $created->id;

                if (! empty($validated['sponsorUsername'])) {
                    $networkArr['sponsored_id'] = Member::query()
                        ->where('username', $validated['sponsorUsername'])
                        ->value('id');
                }

                if (! empty($validated['parentUsername'])) {
                    $networkArr['parent_id'] = Member::query()
                        ->where('username', $validated['parentUsername'])
                        ->value('id');
                }

                $placementService = app(MemberNetworkPlacementService::class);
                $resolvedNetwork = $placementService->resolvePlacement(
                    $created,
                    $created->referral_code,
                    $networkArr['sponsored_id'] ?? null,
                    $networkArr['parent_id'] ?? null,
                    $networkArr['position'] ?? null,
                );

                $networkArr = array_merge($resolvedNetwork, array_filter([
                    'group' => $networkArr['group'] ?? null,
                    'rank' => $networkArr['rank'] ?? null,
                    'generation' => $networkArr['generation'] ?? null,
                    'path' => $networkArr['path'] ?? null,
                ], fn ($value) => $value !== null));

                MemberNetwork::updateOrCreate(
                    ['member_id' => $created->id],
                    $networkArr,
                );

                $sponsor = isset($networkArr['sponsored_id']) ? Member::find($networkArr['sponsored_id']) : null;
                $placementService->updateSponsorRank($sponsor);
            }

            Flux::toast(variant: 'success', text: 'Member created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-memberTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingMemberId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.member.index', $this->getLocationOptions())
            ->layout('layouts.app', ['title' => __('Member')]);
    }

    /**
     * Mengambil opsi pilihan wilayah secara cascading.
     *
     * @return array<string, array<int, string>>
     */
    private function getLocationOptions(): array
    {
        if (! $this->showModal) {
            return [
                'countries' => [],
                'provinces' => [],
                'cities' => [],
                'districts' => [],
                'villages' => [],
            ];
        }

        $profile = $this->profile;

        $countryId = $profile['country_id'] ?? null;
        $provinceId = $profile['province_id'] ?? null;
        $cityId = $profile['city_id'] ?? null;
        $districtId = $profile['district_id'] ?? null;

        return [
            'countries' => Country::where('status', true)->orderBy('name')->pluck('name', 'id')->all(),

            'provinces' => ! empty($countryId)
                ? Province::where('country_id', $countryId)->orderBy('name')->pluck('name', 'id')->all()
                : [],

            'cities' => ! empty($provinceId)
                ? City::where('province_id', $provinceId)->orderBy('name')->pluck('name', 'id')->all()
                : [],

            'districts' => ! empty($cityId)
                ? District::where('city_id', $cityId)->orderBy('name')->pluck('name', 'id')->all()
                : [],

            'villages' => ! empty($districtId)
                ? Village::where('district_id', $districtId)->orderBy('name')->pluck('name', 'id')->all()
                : [],
        ];
    }

    public function updatedProfile($value, $key): void
    {
        // Reset level di bawahnya jika level di atasnya berubah
        $resets = [
            'country_id' => ['province_id', 'city_id', 'district_id', 'village_id'],
            'province_id' => ['city_id', 'district_id', 'village_id'],
            'city_id' => ['district_id', 'village_id'],
            'district_id' => ['village_id'],
        ];

        if (isset($resets[$key])) {
            foreach ($resets[$key] as $field) {
                $this->profile[$field] = null;
            }
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Member $member = null): array
    {
        $rules = [
            'form' => ['array'],
            'form.*' => ['nullable'],
            'profile' => ['array'],
            'profile.*' => ['nullable'],
            'network' => ['array'],
            'network.*' => ['nullable'],
        ];

        foreach (StoreMemberRequest::memberRules($member) as $key => $ruleSet) {
            $rules["form.$key"] = $ruleSet;
        }

        $profileRules = StoreProfileRequest::rules();
        unset($profileRules['member_id']);

        foreach ($profileRules as $key => $ruleSet) {
            $rules["profile.$key"] = $ruleSet;
        }

        $rules['network.sponsored_id'] = ['nullable', 'integer', Rule::exists('members', 'id')];
        $rules['network.parent_id'] = ['nullable', 'integer', Rule::exists('members', 'id')];
        $rules['network.position'] = ['nullable', 'in:left,right'];
        $rules['network.rank'] = ['nullable', 'integer'];
        $rules['network.group'] = ['nullable', 'integer'];
        $rules['sponsorUsername'] = ['nullable', 'string', Rule::exists('members', 'username')];
        $rules['parentUsername'] = ['nullable', 'string', Rule::exists('members', 'username')];

        $rules['form.password'] = $member
            ? ['nullable', 'string', Password::default(), 'confirmed']
            : ['required', 'string', Password::default(), 'confirmed'];
        $rules['form.password_confirmation'] = $member
            ? ['nullable', 'string']
            : ['required', 'string'];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function attributes(): array
    {
        $attributes = [];

        foreach (StoreMemberRequest::attributeLabels() as $key => $value) {
            $attributes["form.$key"] = $value;
        }

        foreach (StoreProfileRequest::attributeLabels() as $key => $value) {
            $attributes["profile.$key"] = $value;
        }

        $attributes['sponsorUsername'] = 'Sponsor Username';
        $attributes['parentUsername'] = 'Parent Username';
        $attributes['form.password'] = 'password';
        $attributes['form.password_confirmation'] = 'password confirmation';

        return $attributes;
    }

    private function hasProfileInput(array $profile): bool
    {
        foreach ($profile as $key => $value) {
            if ($key === 'member_id') {
                continue;
            }

            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }

    private function hasNetworkInput(array $network): bool
    {
        foreach ($network as $key => $value) {
            if ($key === 'member_id') {
                continue;
            }

            if ($value !== null && $value !== '' && $value !== 0) {
                return true;
            }
        }

        return false;
    }

    public function updatedSponsorUsername(): void
    {
        $this->resolveSponsorUsername();
    }

    public function updatedParentUsername(): void
    {
        $this->resolveParentUsername();
    }

    private function resolveSponsorUsername(): void
    {
        if ($this->sponsorUsername === '') {
            $this->sponsorName = null;
            $this->network['sponsored_id'] = null;

            return;
        }

        $member = Member::query()->where('username', $this->sponsorUsername)->first();
        $this->sponsorName = $member?->name;
        $this->network['sponsored_id'] = $member?->id;
    }

    private function resolveParentUsername(): void
    {
        if ($this->parentUsername === '') {
            $this->parentName = null;
            $this->network['parent_id'] = null;

            return;
        }

        $member = Member::query()->where('username', $this->parentUsername)->first();
        $this->parentName = $member?->name;
        $this->network['parent_id'] = $member?->id;
    }

    private function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'status' => 'active',
            'referral_code' => '',
            'email_verified_at' => '',
            'last_login_at' => '',
        ];

        $this->profile = [
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

        $this->network = [
            'member_id' => null,
            'sponsored_id' => null,
            'parent_id' => null,
            'position' => null,
            'path' => null,
            'generation' => 0,
            'group' => 0,
            'rank' => 0,
        ];

        $this->sponsorUsername = '';
        $this->sponsorName = null;
        $this->parentUsername = '';
        $this->parentName = null;
    }
}
