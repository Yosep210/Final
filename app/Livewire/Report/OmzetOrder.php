<?php

namespace App\Livewire\Report;

use App\Models\ProductOrder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Title('Laporan Omzet Order')]
class OmzetOrder extends Component
{
    use AuthorizesRequests, WithPagination;

    public $tab = 'daily'; // 'daily' or 'monthly'
    public $startDate = '';
    public $endDate = '';
    public $startMonth = '';
    public $endMonth = '';

    protected $queryString = [
        'tab' => ['except' => 'daily'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'startMonth' => ['except' => ''],
        'endMonth' => ['except' => ''],
    ];

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $this->startDate = date('Y-m-d', strtotime('-30 days'));
        $this->endDate = date('Y-m-d');
        $this->startMonth = date('Y-m', strtotime('-12 months'));
        $this->endMonth = date('Y-m');
    }

    public function updating(): void
    {
        $this->resetPage();
    }

    public function setTab($tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        if ($this->tab === 'daily') {
            $this->startDate = date('Y-m-d', strtotime('-30 days'));
            $this->endDate = date('Y-m-d');
        } else {
            $this->startMonth = date('Y-m', strtotime('-12 months'));
            $this->endMonth = date('Y-m');
        }
        $this->resetPage();
    }

    public function render()
    {
        $items = [];

        if ($this->tab === 'daily') {
            $query = ProductOrder::query()
                ->whereIn('status', [1, 2])
                ->select([
                    DB::raw("DATE(created_at) as date_omzet"),
                    DB::raw("SUM(CASE WHEN type_order = 'generate' THEN total_omzet ELSE 0 END) as omzet_generate"),
                    DB::raw("SUM(CASE WHEN type_order != 'generate' THEN total_omzet ELSE 0 END) as omzet_order"),
                    DB::raw("SUM(total_omzet) as total_omzet")
                ])
                ->groupBy(DB::raw("DATE(created_at)"));

            if ($this->startDate) {
                $query->whereDate('created_at', '>=', $this->startDate);
            }
            if ($this->endDate) {
                $query->whereDate('created_at', '<=', $this->endDate);
            }

            $results = $query->orderBy('date_omzet', 'desc')->get();

            foreach ($results as $row) {
                $items[] = (object)[
                    'label' => $row->date_omzet,
                    'omzet_generate' => $row->omzet_generate,
                    'omzet_order' => $row->omzet_order,
                    'total_omzet' => $row->total_omzet
                ];
            }
        } else {
            $query = ProductOrder::query()
                ->whereIn('status', [1, 2])
                ->select([
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_omzet"),
                    DB::raw("SUM(CASE WHEN type_order = 'generate' THEN total_omzet ELSE 0 END) as omzet_generate"),
                    DB::raw("SUM(CASE WHEN type_order != 'generate' THEN total_omzet ELSE 0 END) as omzet_order"),
                    DB::raw("SUM(total_omzet) as total_omzet")
                ])
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"));

            if ($this->startMonth) {
                $query->where('created_at', '>=', $this->startMonth . '-01 00:00:00');
            }
            if ($this->endMonth) {
                $query->where('created_at', '<=', $this->endMonth . '-31 23:59:59');
            }

            $results = $query->orderBy('month_omzet', 'desc')->get();

            foreach ($results as $row) {
                $items[] = (object)[
                    'label' => $row->month_omzet,
                    'omzet_generate' => $row->omzet_generate,
                    'omzet_order' => $row->omzet_order,
                    'total_omzet' => $row->total_omzet
                ];
            }
        }

        // Manual Pagination
        $page = $this->paginators['page'] ?? 1;
        $perPage = 10;
        $totalItems = count($items);
        $paginatedItems = array_slice($items, ($page - 1) * $perPage, $perPage);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($paginatedItems, $totalItems, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query()
        ]);

        return view('livewire.report.omzet-order', [
            'orders' => $paginator
        ])->layout('layouts.app', ['title' => __('Laporan Omzet Order')]);
    }
}
