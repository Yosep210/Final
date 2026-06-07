<?php

namespace App\Livewire\Report;

use App\Models\CommissionLog;
use App\Models\ProductOrder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Laporan Budgeting')]
class Budgeting extends Component
{
    use AuthorizesRequests, WithPagination;

    public $startMonth = '';

    public $endMonth = '';

    protected $queryString = [
        'startMonth' => ['except' => ''],
        'endMonth' => ['except' => ''],
    ];

    public function mount(): void
    {
        if (! auth()->user() || ! auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        // CodeIgniter defaults to past 6 months (5 months ago to current)
        $this->startMonth = date('Y-m', strtotime('-5 months'));
        $this->endMonth = date('Y-m');
    }

    public function updating(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->startMonth = date('Y-m', strtotime('-5 months'));
        $this->endMonth = date('Y-m');
        $this->resetPage();
    }

    public function render()
    {
        // 1. Get months in the range
        $start = strtotime($this->startMonth.'-01');
        $end = strtotime($this->endMonth.'-01');

        if ($start > $end) {
            $end = $start;
        }

        $months = [];
        $curr = $start;
        while ($curr <= $end) {
            $months[] = date('Y-m', $curr);
            $curr = strtotime('+1 month', $curr);
        }
        $months = array_reverse($months); // Descending order

        // 2. Query monthly omzet & BV
        $omzetQuery = ProductOrder::query()
            ->whereIn('status', [1, 2])
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(CASE WHEN type_order = 'register' THEN total_omzet ELSE 0 END) as omzet_reg"),
                DB::raw("SUM(CASE WHEN type_order = 'register' THEN total_bv ELSE 0 END) as bv_reg"),
                DB::raw("SUM(CASE WHEN type_order = 'manual_ro' THEN total_omzet ELSE 0 END) as omzet_ro"),
                DB::raw("SUM(CASE WHEN type_order = 'manual_ro' THEN total_bv ELSE 0 END) as bv_ro"),
                DB::raw('SUM(total_omzet) as total_omzet'),
                DB::raw('SUM(total_bv) as total_bv'),
            ])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"));

        if ($this->startMonth) {
            $omzetQuery->where('created_at', '>=', $this->startMonth.'-01 00:00:00');
        }
        if ($this->endMonth) {
            $omzetQuery->where('created_at', '<=', $this->endMonth.'-31 23:59:59');
        }

        $omzetData = $omzetQuery->get()->keyBy('month')->all();

        // 3. Query monthly commission payout breakdown by type
        $commissionQuery = CommissionLog::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                'type',
                DB::raw('SUM(gross_commission) as total_commission'),
            ])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), 'type');

        if ($this->startMonth) {
            $commissionQuery->where('created_at', '>=', $this->startMonth.'-01 00:00:00');
        }
        if ($this->endMonth) {
            $commissionQuery->where('created_at', '<=', $this->endMonth.'-31 23:59:59');
        }

        $commissionData = [];
        foreach ($commissionQuery->get() as $row) {
            $commissionData[$row->month][$row->type] = $row->total_commission;
        }

        // Budgeting config mapping
        $cfg_budget = [
            'sponsor' => ['percent' => 24, 'label' => 'Sponsor', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900'],
            'pairing' => ['percent' => 24, 'label' => 'Pairing', 'color' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-indigo-900'],
            'unilevel' => ['percent' => 8, 'label' => 'Level', 'color' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/20 dark:text-sky-400 dark:border-sky-900'],
            'generation' => ['percent' => 7.2, 'label' => 'Generasi', 'color' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900'],
            'reward' => ['percent' => 0, 'label' => 'Reward', 'color' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900'],
        ];
        $kurs_bv = 1000;

        // 4. Assemble the budget rows
        $items = [];
        foreach ($months as $month) {
            $omzet = $omzetData[$month] ?? null;

            $omzet_reg = $omzet ? $omzet->omzet_reg : 0;
            $bv_reg = $omzet ? $omzet->bv_reg : 0;
            $omzet_ro = $omzet ? $omzet->omzet_ro : 0;
            $bv_ro = $omzet ? $omzet->bv_ro : 0;
            $total_omzet = $omzet ? $omzet->total_omzet : 0;
            $total_bv = $omzet ? $omzet->total_bv : 0;

            $budget_data = [];
            $total_budget = 0;
            $total_bonus = 0;

            foreach ($cfg_budget as $type => $conf) {
                $percent = $conf['percent'];
                $bonus = $commissionData[$month][$type] ?? 0;
                $budget = ($total_bv * $percent * $kurs_bv) / 100;
                $saldo = $budget - $bonus;

                $total_budget += $budget;
                $total_bonus += $bonus;

                $budget_data[$type] = [
                    'budget' => $budget,
                    'bonus' => $bonus,
                    'saldo' => $saldo,
                ];
            }

            $total_saldo = $total_budget - $total_bonus;
            $percentage = $total_budget > 0 ? ($total_saldo / $total_budget) * 100 : 0;

            $items[] = (object) [
                'period' => $month,
                'omzet_reg' => $omzet_reg,
                'bv_reg' => $bv_reg,
                'omzet_ro' => $omzet_ro,
                'bv_ro' => $bv_ro,
                'total_omzet' => $total_omzet,
                'total_bv' => $total_bv,
                'total_budget' => $total_budget,
                'total_bonus' => $total_bonus,
                'total_saldo' => $total_saldo,
                'percentage' => $percentage,
                'budget_data' => $budget_data,
            ];
        }

        // Paginate manually
        $page = $this->paginators['page'] ?? 1;
        $perPage = 10;
        $totalItems = count($items);
        $paginatedItems = array_slice($items, ($page - 1) * $perPage, $perPage);
        $paginator = new LengthAwarePaginator($paginatedItems, $totalItems, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('livewire.report.budgeting', [
            'orders' => $paginator,
            'cfg_budget' => $cfg_budget,
        ])->layout('layouts.app', ['title' => __('Laporan Budgeting')]);
    }
}
