<?php

namespace App\Livewire\Report;

use App\Models\ProductOrder;
use App\Models\CommissionLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Title('Laporan Omzet Harian')]
class OmzetDaily extends Component
{
    use AuthorizesRequests, WithPagination;

    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $this->startDate = date('Y-m-d', strtotime('-30 days'));
        $this->endDate = date('Y-m-d');
    }

    public function updating(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->startDate = date('Y-m-d', strtotime('-30 days'));
        $this->endDate = date('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        // 1. Get daily omzet from product_orders grouped by date
        $omzetQuery = ProductOrder::query()
            ->whereIn('status', [1, 2])
            ->select([
                DB::raw('DATE(created_at) as date_omzet'),
                DB::raw("SUM(CASE WHEN type_order = 'register' THEN total_omzet ELSE 0 END) as total_register"),
                DB::raw("SUM(CASE WHEN type_order = 'manual_ro' THEN total_omzet ELSE 0 END) as total_ro"),
                DB::raw("SUM(total_omzet) as total_omzet"),
                DB::raw("SUM(total_bv) as total_bv")
            ])
            ->groupBy(DB::raw('DATE(created_at)'));

        if ($this->startDate) {
            $omzetQuery->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $omzetQuery->whereDate('created_at', '<=', $this->endDate);
        }

        $omzetResults = $omzetQuery->orderBy('date_omzet', 'desc')->get();

        // 2. Get daily commissions from commission_logs grouped by date
        $commissionQuery = CommissionLog::query()
            ->select([
                DB::raw('DATE(created_at) as date_commission'),
                DB::raw('SUM(gross_commission) as total_commission')
            ])
            ->groupBy(DB::raw('DATE(created_at)'));

        if ($this->startDate) {
            $commissionQuery->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $commissionQuery->whereDate('created_at', '<=', $this->endDate);
        }

        $commissionResults = $commissionQuery->get()->pluck('total_commission', 'date_commission')->all();

        // 3. Map both results together
        $items = [];
        foreach ($omzetResults as $row) {
            $date = $row->date_omzet;
            $payout = $commissionResults[$date] ?? 0;
            $percentage = $row->total_omzet > 0 ? ($payout / $row->total_omzet) * 100 : 0;

            $items[] = (object)[
                'date' => $date,
                'register' => $row->total_register,
                'ro' => $row->total_ro,
                'total_omzet' => $row->total_omzet,
                'total_bv' => $row->total_bv,
                'payout' => $payout,
                'percentage' => $percentage
            ];
        }

        // Paginate items manually
        $page = $this->paginators['page'] ?? 1;
        $perPage = 10;
        $totalItems = count($items);
        $paginatedItems = array_slice($items, ($page - 1) * $perPage, $perPage);
        $orders = new \Illuminate\Pagination\LengthAwarePaginator($paginatedItems, $totalItems, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query()
        ]);

        return view('livewire.report.omzet-daily', [
            'orders' => $orders
        ])->layout('layouts.app', ['title' => __('Laporan Omzet Harian')]);
    }
}
