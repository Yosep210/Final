<?php

namespace App\Livewire\Commission;

use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Statement Commission')]
class Statement extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    public function render()
    {
        return view('livewire.commission.statement')
            ->layout('layouts.app', ['title' => __('Statement Commission')]);
    }
}
