<?php

namespace App\Livewire\AutoRo;

use App\Models\AutoRoLog;
use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Auto RO Logs')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showDetailModal = false;

    public ?Member $selectedMember = null;

    public array $memberAutoRoLogs = [];

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    #[On('auto-ro:view-detail')]
    public function openDetail(int $memberId): void
    {
        $this->selectedMember = Member::findOrFail($memberId);
        $this->memberAutoRoLogs = AutoRoLog::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedMember = null;
        $this->memberAutoRoLogs = [];
    }

    public function render()
    {
        return view('livewire.auto-ro.index')
            ->layout('layouts.app', ['title' => __('Auto RO Logs')]);
    }
}
