<?php

namespace App\Livewire\Settings;

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteMemberForm extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated member.
     */
    public function deleteMember(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        $member = Auth::user();

        // Delete member account SEBELUM logout
        $member->delete();

        // Kemudian logout
        $logout();

        $this->redirect('/', navigate: true);
    }
}
