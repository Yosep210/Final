<?php

namespace App\Livewire\Commission;

use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Commission List')]
class Index extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    public function render()
    {
        return view('livewire.commission.index')
            ->layout('layouts.app', ['title' => __('Commission List')]);
    }
}
