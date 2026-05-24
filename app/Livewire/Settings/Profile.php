<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->username = Auth::user()->username;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated member.
     */
    public function updateProfileInformation(): void
    {
        $member = Auth::user();

        $validated = $this->validate($this->profileRules($member->id));

        $member->fill($validated);

        if ($member->isDirty('email')) {
            $member->email_verified_at = null;
        }

        $member->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
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
