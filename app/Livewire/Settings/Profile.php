<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use App\Models\Village;
use Flux\Flux;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile settings')]
class Profile extends Component
{
    use ProfileValidationRules;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public ?string $gender = '';

    public ?string $birth_date = '';

    public ?string $id_card_number = '';

    public ?string $npwp_number = '';

    public ?string $phone = '';

    public ?int $country_id = null;

    public ?int $province_id = null;

    public ?int $city_id = null;

    public ?int $district_id = null;

    public ?int $village_id = null;

    public ?string $address = null;

    public array $countries = [];

    public array $provinces = [];

    public array $cities = [];

    public array $districts = [];

    public array $villages = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->username = Auth::user()->username;
        $this->email = Auth::user()->email;
        $this->gender = Auth::user()->profile?->gender;
        $this->birth_date = Auth::user()->profile?->birth_date?->format('Y-m-d');
        $this->id_card_number = Auth::user()->profile?->id_card_number;
        $this->npwp_number = Auth::user()->profile?->npwp_number;
        $this->phone = Auth::user()->profile?->phone;
        $this->country_id = Auth::user()->profile?->country_id;
        $this->province_id = Auth::user()->profile?->province_id;
        $this->city_id = Auth::user()->profile?->city_id;
        $this->district_id = Auth::user()->profile?->district_id;
        $this->village_id = Auth::user()->profile?->village_id;
        $this->address = Auth::user()->profile?->address;

        $this->countries = Country::where('status', true)
            ->orderBy('nice_name')
            ->get()
            ->pluck('display_name', 'id')
            ->all();

        $this->loadLocationOptions();
    }

    /**
     * Update the profile information for the currently authenticated member.
     */
    public function updateProfileInformation(): void
    {
        $member = Auth::user();

        $validated = $this->validate($this->profileRules($member->id));

        $member->fill(Arr::only($validated, ['name', 'username', 'email']));

        if ($member->isDirty('email')) {
            $member->email_verified_at = null;
        }

        $member->save();

        $profileData = Arr::only($validated, [
            'gender',
            'birth_date',
            'phone',
            'country_id',
            'province_id',
            'city_id',
            'district_id',
            'village_id',
            'address',
            'id_card_number',
            'npwp_number',
        ]);

        $profileData = array_map(fn ($value) => $value === '' ? null : $value, $profileData);

        $member->profile()->updateOrCreate(
            ['member_id' => $member->id],
            $profileData,
        );

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    public function updatedCountryId(?int $value): void
    {
        $this->province_id = null;
        $this->city_id = null;
        $this->district_id = null;
        $this->village_id = null;
        $this->loadLocationOptions();
    }

    public function updatedProvinceId(?int $value): void
    {
        $this->city_id = null;
        $this->district_id = null;
        $this->village_id = null;
        $this->loadLocationOptions();
    }

    public function updatedCityId(?int $value): void
    {
        $this->district_id = null;
        $this->village_id = null;
        $this->loadLocationOptions();
    }

    public function updatedDistrictId(?int $value): void
    {
        $this->village_id = null;
        $this->loadLocationOptions();
    }

    private function loadLocationOptions(): void
    {
        $this->provinces = $this->country_id
            ? Province::where('country_id', $this->country_id)
                ->orderBy('name')
                ->get()
                ->pluck('display_name', 'id')
                ->all()
            : [];

        $this->cities = $this->province_id
            ? City::where('province_id', $this->province_id)
                ->orderBy('name')
                ->get()
                ->pluck('display_name', 'id')
                ->all()
            : [];

        $this->districts = $this->city_id
            ? District::where('city_id', $this->city_id)
                ->orderBy('name')
                ->get()
                ->pluck('display_name', 'id')
                ->all()
            : [];

        $this->villages = $this->district_id
            ? Village::where('district_id', $this->district_id)
                ->orderBy('name')
                ->get()
                ->pluck('display_name', 'id')
                ->all()
            : [];
    }

    /**
     * Send an email verification notification to the current member.
     */
    public function resendVerificationNotification(): void
    {
        $member = Auth::user();

        if ($member->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $member->sendEmailVerificationNotification();

        Flux::toast(text: __('A new verification link has been sent to your email address.'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteMember(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}
