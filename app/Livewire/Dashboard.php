<?php

namespace App\Livewire;

use App\Models\AutoRoLog;
use App\Models\CommissionLog;
use App\Models\EwalletLog;
use App\Models\Member;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public bool $isAdmin = false;

    // Admin stats
    public int $totalMembers = 0;

    public int $activeMembers = 0;

    public float $totalCommissions = 0;

    public float $paidCommissions = 0;

    public float $pendingCommissions = 0;

    public float $ewalletBalance = 0;

    public float $autoRoAmount = 0;

    public float $totalWithdrawals = 0;

    public array $recentMembers = [];

    public array $recentCommissions = [];

    // Member stats
    public string $myRank = 'Member';

    public int $mySponsorsCount = 0;

    public float $myEwalletBalance = 0;

    public float $myAutoRo = 0;

    public float $myCommissions = 0;

    public array $myRecentTransactions = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->isAdmin = $user->hasRole('Admin');

        if ($this->isAdmin) {
            $this->loadAdminStats();
        } else {
            $this->loadMemberStats($user);
        }
    }

    private function loadAdminStats(): void
    {
        // Exclude Admin & Staff members from calculations
        $memberIdsQuery = Member::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Staff']);
        });

        $this->totalMembers = (clone $memberIdsQuery)->count();
        $this->activeMembers = (clone $memberIdsQuery)->where('status', 'active')->count();

        // Commission logs
        $commissionLogsQuery = CommissionLog::whereIn('member_id', function ($query) {
            $query->select('id')
                ->from('members')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->whereColumn('model_has_roles.model_id', 'members.id')
                        ->where('model_has_roles.model_type', Member::class)
                        ->whereIn('roles.name', ['Admin', 'Staff']);
                });
        });

        $this->totalCommissions = (clone $commissionLogsQuery)->sum('gross_commission');
        $this->paidCommissions = (clone $commissionLogsQuery)->where('is_paid', true)->sum('net_commission');
        $this->pendingCommissions = (clone $commissionLogsQuery)->where('is_paid', false)->sum('gross_commission');

        // eWallet total system balance
        $inAmount = EwalletLog::where('type', 'IN')->sum('amount');
        $outAmount = EwalletLog::where('type', 'OUT')->sum('amount');
        $this->ewalletBalance = $inAmount - $outAmount;

        // Auto RO total
        $this->autoRoAmount = AutoRoLog::sum('amount');

        // Total Withdrawals (Success)
        $this->totalWithdrawals = Withdrawal::where('status', 1)->sum('nominal');

        // Recent Members
        $this->recentMembers = Member::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Staff']);
        })
            ->latest()
            ->limit(5)
            ->get()
            ->toArray();

        // Recent Commissions
        $this->recentCommissions = CommissionLog::with('member')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'username' => $log->member->username ?? 'Unknown',
                'type' => $log->type,
                'amount' => $log->gross_commission,
                'created_at' => $log->created_at ? $log->created_at->format('d M Y H:i') : '-',
            ])
            ->toArray();
    }

    private function loadMemberStats(Member $user): void
    {
        $memberId = $user->id;

        // Rank
        $network = DB::table('member_networks')->where('member_id', $memberId)->first();
        $this->myRank = $network->current_rank ?? 'Member';

        // Sponsor Count
        $this->mySponsorsCount = DB::table('member_networks')->where('sponsored_id', $memberId)->count();

        // eWallet
        $inAmount = EwalletLog::where('member_id', $memberId)->where('type', 'IN')->sum('amount');
        $outAmount = EwalletLog::where('member_id', $memberId)->where('type', 'OUT')->sum('amount');
        $this->myEwalletBalance = $inAmount - $outAmount;

        // Auto RO
        $this->myAutoRo = AutoRoLog::where('member_id', $memberId)->sum('amount');

        // Total Commission
        $this->myCommissions = CommissionLog::where('member_id', $memberId)->sum('net_commission');

        // Recent eWallet Transactions
        $this->myRecentTransactions = EwalletLog::where('member_id', $memberId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($log) => [
                'type' => $log->type,
                'category' => $log->category,
                'amount' => $log->amount,
                'description' => $log->description,
                'created_at' => $log->created_at ? $log->created_at->format('d M Y H:i') : '-',
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app', ['title' => __('Dashboard')]);
    }
}
