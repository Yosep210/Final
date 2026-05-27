<?php

namespace App\Livewire\Member;

use App\Actions\Member\CreateMemberAction;
use App\Actions\Member\Profile\CreateProfileAction;
use App\Actions\Member\Profile\UpdateProfileAction;
use App\Actions\Member\UpdateMemberAction;
use App\Data\MemberData;
use App\Data\MemberProfileData;
use App\Http\Requests\Member\Profile\StoreProfileRequest;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Models\Country;
use App\Models\Member;
use App\Models\MemberNetwork;
use App\Services\MemberNetworkPlacementService;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Member')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $canManageMembers = false;

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

    public ?string $phoneCode = null;

    public string $phoneNumber = '';

    public ?int $countryId = null;

    public string $sponsorUsername = '';

    public ?string $sponsorName = null;

    public string $parentUsername = '';

    public ?string $parentName = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
        $this->canManageMembers = auth()->user()?->can('create', Member::class) ?? false;
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
        $member = Member::query()->with(['profile', 'network.sponsor', 'network.parent'])->findOrFail($memberId);
        $this->authorize('update', $member);

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

        $this->fillProfileForm($member);
        $this->fillNetworkForm($member);

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        // Combine phone code + phone number
        if ($this->phoneCode && $this->phoneNumber) {
            $this->profile['phone'] = $this->phoneCode.preg_replace('/^0+/', '', $this->phoneNumber);
        } elseif ($this->phoneNumber) {
            $this->profile['phone'] = $this->phoneNumber;
        }

        $member = $this->editingMemberId
            ? Member::query()->findOrFail($this->editingMemberId)
            : null;

        if ($member) {
            $this->authorize('update', $member);
        } else {
            $this->authorize('create', Member::class);
        }

        $validated = $this->validate($this->rules($member), [], $this->attributes());

        if (! $member) {
            $temporaryPassword = $this->generateTemporaryPassword();
            $validated['form']['password'] = $temporaryPassword;
            $validated['form']['password_confirmation'] = $temporaryPassword;
        }

        $memberData = MemberData::fromArray($validated['form']);

        if ($member) {
            UpdateMemberAction::run($member, $memberData);

            if ($this->hasProfileInput($validated['profile'] ?? [])) {
                $profileArr = $validated['profile'];
                $profileArr['member_id'] = $member->id;
                $profileData = MemberProfileData::fromArray($profileArr);

                if ($member->profile) {
                    UpdateProfileAction::run($member->profile, $profileData);
                } else {
                    CreateProfileAction::run($profileData);
                }
            }

            $this->syncNetwork($member, $validated);
            Flux::toast(variant: 'success', text: 'Member updated successfully.');
        } else {
            $created = CreateMemberAction::run($memberData);

            if ($this->hasProfileInput($validated['profile'] ?? [])) {
                $profileArr = $validated['profile'];
                $profileArr['member_id'] = $created->id;
                $profileData = MemberProfileData::fromArray($profileArr);
                CreateProfileAction::run($profileData);
            }

            $this->syncNetwork($created, $validated);
            $this->sendPasswordSetupLink($created);

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
        return [
            'countries' => Country::where('status', true)->orderBy('nice_name')->pluck('nice_name', 'id')->all(),
        ];
    }

    public function updatedProfile($value, $key): void
    {
        if ($key === 'country_id') {
            $this->phoneCode = $this->resolvePhoneCodeFromCountry($value);
            $this->phoneNumber = ''; // Reset nomor saat negara berubah
        }

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

        $rules['form.password'] = ['nullable', 'string', Password::default(), 'confirmed'];
        $rules['form.password_confirmation'] = ['nullable', 'string'];

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

    private function syncNetwork(Member $member, array $validated): void
    {
        if (! $this->hasNetworkInput($validated['network'] ?? []) && empty($validated['sponsorUsername']) && empty($validated['parentUsername'])) {
            return;
        }

        $networkArr = $validated['network'] ?? [];
        $networkArr['member_id'] = $member->id;

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
            $member,
            null,
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
            ['member_id' => $member->id],
            $networkArr,
        );

        $sponsor = isset($networkArr['sponsored_id']) ? Member::find($networkArr['sponsored_id']) : null;
        $placementService->updateSponsorRank($sponsor);
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

    private function resolveDefaultCountryId(): ?int
    {
        // Indonesia: iso bisa 'ID' atau 'id', nice_name='Indonesia'
        return Country::where('status', true)
            ->where(function ($query) {
                $query->whereRaw('UPPER(iso) = ?', ['ID'])
                    ->orWhere('nice_name', 'Indonesia');
            })
            ->first()?->id;
    }

    private function resolvePhoneCodeFromCountry(?int $countryId): ?string
    {
        if (! $countryId) {
            return null;
        }

        $phonecode = Country::where('id', $countryId)->value('phonecode');

        return $phonecode ? '+'.ltrim((string) $phonecode, '+') : null;
    }

    private function generateTemporaryPassword(): string
    {
        return 'Tmp#'.Str::random(12).'9a';
    }

    private function sendPasswordSetupLink(Member $member): void
    {
        try {
            $status = PasswordBroker::broker(config('fortify.passwords'))->sendResetLink([
                'email' => $member->email,
            ]);

            if ($status !== PasswordBroker::RESET_LINK_SENT) {
                Flux::toast(variant: 'warning', text: 'Member created, but the password setup email could not be sent.');
            }
        } catch (\Throwable) {
            Flux::toast(variant: 'warning', text: 'Member created, but the password setup email could not be sent.');
        }
    }

    private function fillProfileForm(Member $member): void
    {
        $profile = $member->profile;

        if (! $profile) {
            return;
        }

        $this->profile = [
            'member_id' => $member->id,
            'gender' => $profile->gender,
            'birth_date' => $profile->birth_date?->format('Y-m-d'),
            'phone' => $profile->phone,
            'profile_photo' => $profile->profile_photo,
            'country_id' => $profile->country_id !== null ? (string) $profile->country_id : '',
            'province_id' => $profile->province_id,
            'city_id' => $profile->city_id,
            'district_id' => $profile->district_id,
            'village_id' => $profile->village_id,
            'address' => $profile->address,
            'id_card_number' => $profile->id_card_number,
            'id_card_photo' => $profile->id_card_photo,
            'npwp_number' => $profile->npwp_number,
        ];

        $this->phoneCode = $this->resolvePhoneCodeFromCountry($profile->country_id);
        $this->phoneNumber = $this->extractPhoneNumber($profile->phone, $this->phoneCode);
    }

    private function fillNetworkForm(Member $member): void
    {
        $network = $member->network;

        if (! $network) {
            return;
        }

        $this->network = [
            'member_id' => $member->id,
            'sponsored_id' => $network->sponsored_id,
            'parent_id' => $network->parent_id,
            'position' => $network->position,
            'path' => $network->path,
            'generation' => $network->generation,
            'group' => $network->group,
            'rank' => $network->rank,
        ];

        $this->sponsorUsername = $network->sponsor?->username ?? '';
        $this->sponsorName = $network->sponsor?->name;
        $this->parentUsername = $network->parent?->username ?? '';
        $this->parentName = $network->parent?->name;
    }

    private function extractPhoneNumber(?string $phone, ?string $phoneCode): string
    {
        if (! $phone) {
            return '';
        }

        if ($phoneCode && str_starts_with($phone, $phoneCode)) {
            return ltrim(substr($phone, strlen($phoneCode)), '0');
        }

        return $phone;
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

        $defaultCountryId = $this->resolveDefaultCountryId();

        $this->profile = [
            'member_id' => null,
            'gender' => null,
            'birth_date' => null,
            'phone' => null,
            'profile_photo' => null,
            'country_id' => $defaultCountryId !== null ? (string) $defaultCountryId : '',
            'province_id' => null,
            'city_id' => null,
            'district_id' => null,
            'village_id' => null,
            'address' => null,
        ];

        $this->phoneCode = $this->resolvePhoneCodeFromCountry($defaultCountryId);
        $this->phoneNumber = '';

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
