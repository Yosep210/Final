<?php

namespace App\Livewire\Commission;

use App\Models\CommissionLog;
use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Commission List')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showDetailModal = false;

    public ?Member $selectedMember = null;

    public array $memberCommissions = [];

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    #[On('commission:view-detail')]
    public function openDetail(int $memberId): void
    {
        $this->selectedMember = Member::findOrFail($memberId);
        $this->memberCommissions = CommissionLog::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedMember = null;
        $this->memberCommissions = [];
    }

    public function render()
    {
        return view('livewire.commission.index')
            ->layout('layouts.app', ['title' => __('Commission List')]);
    }
}
