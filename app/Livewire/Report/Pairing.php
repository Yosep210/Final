<?php

namespace App\Livewire\Report;

use App\Models\CommissionLog;
use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Pairing Qualified')]
class Pairing extends Component
{
    use AuthorizesRequests;

    public bool $showDetailModal = false;

    public ?Member $selectedMember = null;

    public array $memberLogs = [];

    public float $totalLeft = 0;

    public float $totalRight = 0;

    public float $totalMatched = 0;

    public function mount(): void
    {
        if (! auth()->user()) {
            abort(403);
        }

        // If not Admin, show their own history immediately
        if (! auth()->user()->hasRole('Admin')) {
            $this->openDetail(auth()->user()->id);
        }
    }

    #[On('pairing:view-detail')]
    public function openDetail(int $memberId): void
    {
        $this->selectedMember = Member::findOrFail($memberId);
        $this->memberLogs = CommissionLog::where('member_id', $memberId)
            ->where('type', 'pairing')
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();

        $this->totalLeft = CommissionLog::where('member_id', $memberId)->where('type', 'pairing')->sum('left_volume');
        $this->totalRight = CommissionLog::where('member_id', $memberId)->where('type', 'pairing')->sum('right_volume');
        $this->totalMatched = CommissionLog::where('member_id', $memberId)->where('type', 'pairing')->sum('matched_volume');

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        // Non-admin can't close the detail since it is their only view
        if (auth()->user()->hasRole('Admin')) {
            $this->selectedMember = null;
            $this->memberLogs = [];
        }
    }

    public function render()
    {
        return view('livewire.report.pairing', [
            'isAdmin' => auth()->user()->hasRole('Admin'),
        ])->layout('layouts.app', ['title' => __('Laporan Pairing Qualified')]);
    }
}
