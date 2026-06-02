<?php

namespace App\Livewire\Withdraw;

use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Withdraw List')]
class Index extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    public function render()
    {
        return view('livewire.withdraw.index')
            ->layout('layouts.app', ['title' => __('Withdraw List')]);
    }
}
