<?php

namespace App\Livewire\Wallet;

use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('eWallet Logs')]
class Index extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    public function render()
    {
        return view('livewire.wallet.index')
            ->layout('layouts.app', ['title' => __('eWallet Logs')]);
    }
}
