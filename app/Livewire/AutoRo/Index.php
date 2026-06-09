<?php

namespace App\Livewire\AutoRo;

use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Auto RO Logs')]
class Index extends Component
{
    use AuthorizesRequests;

    public string $activeTab = 'saldo';

    public bool $showDetailModal = false;

    public bool $showMonthDetailModal = false;

    public ?Member $selectedMember = null;

    public array $memberAutoRoLogs = [];

    public ?string $selectedMonthRo = null;

    public array $monthlyAutoRoLogs = [];

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    public function setTab(string $tab): void
    {
        $allowedTabs = ['saldo', 'history', 'monthly'];

        $this->activeTab = in_array($tab, $allowedTabs, true) ? $tab : 'saldo';
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

    #[On('auto-ro:view-month-detail')]
    public function openMonthDetail(string $monthRo): void
    {
        $this->selectedMonthRo = $monthRo;
        $this->monthlyAutoRoLogs = AutoRoLog::query()
            ->with('member')
            ->whereRaw('DATE_FORMAT(auto_ro_logs.created_at, "%Y-%m-01") = ?', [$monthRo])
            ->orderByDesc('created_at')
            ->get()
            ->all();

        $this->showMonthDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedMember = null;
        $this->memberAutoRoLogs = [];
    }

    public function closeMonthDetail(): void
    {
        $this->showMonthDetailModal = false;
        $this->selectedMonthRo = null;
        $this->monthlyAutoRoLogs = [];
    }

    public function render()
    {
        return view('livewire.auto-ro.index')
            ->layout('layouts.app', ['title' => __('Auto RO Logs')]);
    }
}
