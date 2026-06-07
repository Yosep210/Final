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

#[Title('Laporan Omzet Bulanan')]
class OmzetMonthly extends Component
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

        $this->startMonth = date('Y-m', strtotime('-12 months'));
        $this->endMonth = date('Y-m');
    }

    public function updating(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->startMonth = date('Y-m', strtotime('-12 months'));
        $this->endMonth = date('Y-m');
        $this->resetPage();
    }

    public function render()
    {
        // 1. Query monthly omzet from product_orders
        $omzetQuery = ProductOrder::query()
            ->whereIn('status', [1, 2])
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_omzet"),
                DB::raw("SUM(CASE WHEN type_order = 'register' THEN total_omzet ELSE 0 END) as total_register"),
                DB::raw("SUM(CASE WHEN type_order = 'manual_ro' THEN total_omzet ELSE 0 END) as total_ro"),
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

        $omzetResults = $omzetQuery->orderBy('month_omzet', 'desc')->get();

        // 2. Query monthly commissions
        $commissionQuery = CommissionLog::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_commission"),
                DB::raw('SUM(gross_commission) as total_commission'),
            ])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"));

        if ($this->startMonth) {
            $commissionQuery->where('created_at', '>=', $this->startMonth.'-01 00:00:00');
        }
        if ($this->endMonth) {
            $commissionQuery->where('created_at', '<=', $this->endMonth.'-31 23:59:59');
        }

        $commissionResults = $commissionQuery->get()->pluck('total_commission', 'month_commission')->all();

        // 3. Map together
        $items = [];
        foreach ($omzetResults as $row) {
            $month = $row->month_omzet;
            $payout = $commissionResults[$month] ?? 0;
            $percentage = $row->total_omzet > 0 ? ($payout / $row->total_omzet) * 100 : 0;

            $items[] = (object) [
                'month' => $month,
                'register' => $row->total_register,
                'ro' => $row->total_ro,
                'total_omzet' => $row->total_omzet,
                'total_bv' => $row->total_bv,
                'payout' => $payout,
                'percentage' => $percentage,
            ];
        }

        // Paginate manually
        $page = $this->paginators['page'] ?? 1;
        $perPage = 10;
        $totalItems = count($items);
        $paginatedItems = array_slice($items, ($page - 1) * $perPage, $perPage);
        $orders = new LengthAwarePaginator($paginatedItems, $totalItems, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('livewire.report.omzet-monthly', [
            'orders' => $orders,
        ])->layout('layouts.app', ['title' => __('Laporan Omzet Bulanan')]);
    }
}
