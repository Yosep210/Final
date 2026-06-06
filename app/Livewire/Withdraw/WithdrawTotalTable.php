<?php

namespace App\Livewire\Withdraw;

use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class WithdrawTotalTable extends PowerGridComponent
{
    public string $tableName = 'withdrawTotalTable';

    public string $sortField = 'datewd';

    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        return [
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $allowedSort = [
            'datewd' => 'datewd',
            'trx' => 'trx',
            'total_nominal' => 'total_nominal',
            'total_receipt' => 'total_receipt',
            'total_admin' => 'total_admin',
            'total_transferred' => 'total_transferred',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'datewd';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $rawSortMap = [
            'datewd' => 'DATE(created_at)',
            'trx' => 'COUNT(id)',
            'total_nominal' => 'SUM(nominal)',
            'total_receipt' => 'SUM(nominal_receipt)',
            'total_admin' => 'SUM(admin_fund)',
            'total_transferred' => 'SUM(nominal_receipt + admin_fund)',
        ];

        $rawSort = $rawSortMap[$sortColumn] ?? 'DATE(created_at)';

        return Withdrawal::query()
            ->select([
                DB::raw('DATE(created_at) as datewd'),
                DB::raw('COUNT(id) as trx'),
                DB::raw('SUM(nominal) as total_nominal'),
                DB::raw('SUM(nominal_receipt) as total_receipt'),
                DB::raw('SUM(admin_fund) as total_admin'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('SUM(auto_ro) as total_auto_ro'),
                DB::raw('SUM(nominal_receipt + admin_fund) as total_transferred'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$rawSort.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('datewd_formatted', fn ($row) => $row->datewd ? Carbon::parse($row->datewd)->locale('id')?->isoFormat('DD MMM YY') : '-')
            ->add('trx_formatted', fn ($row) => number_format((float) ($row->trx ?? 0), 0))
            ->add('keterangan', function ($row) {
                $nominal = number_format((float) ($row->total_nominal ?? 0), 0);
                $fee = number_format((float) ($row->total_admin ?? 0), 0);
                $tax = number_format((float) ($row->total_tax ?? 0), 0);
                $ro = number_format((float) ($row->total_auto_ro ?? 0), 0);

                $html = '<div class="text-[11px] font-mono space-y-0.5 leading-normal text-zinc-600 dark:text-zinc-400">';
                $html .= '<div>Withdrawal : <strong>Rp '.$nominal.'</strong></div>';
                if ($row->total_tax > 0) {
                    $html .= '<div>Pajak : <strong class="text-rose-500">-Rp '.$tax.'</strong></div>';
                }
                if ($row->total_auto_ro > 0) {
                    $html .= '<div>Auto-RO : <strong class="text-rose-500">-Rp '.$ro.'</strong></div>';
                }
                $html .= '<div>Fee : <strong class="text-rose-500">-Rp '.$fee.'</strong></div>';
                $html .= '</div>';

                return $html;
            })
            ->add('nominal_formatted', fn ($row) => number_format((float) ($row->total_receipt ?? 0), 0))
            ->add('fee_formatted', fn ($row) => number_format((float) ($row->total_admin ?? 0), 0))
            ->add('total_transferred_formatted', fn ($row) => number_format((float) ($row->total_transferred ?? 0), 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Tanggal', 'datewd_formatted', 'datewd')->sortable(),
            Column::make('Jumlah Transaksi', 'trx_formatted', 'trx')->sortable(),
            Column::make('Keterangan', 'keterangan'),
            Column::make('Nominal', 'nominal_formatted', 'total_receipt')->sortable(),
            Column::make('Fee Transfer', 'fee_formatted', 'total_admin')->sortable(),
            Column::make('Nominal Transfered', 'total_transferred_formatted', 'total_transferred')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('datewd_formatted', 'withdrawals.created_at'),
            Filter::inputText('trx_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }
                    $normalizedSearch = preg_replace('/[^0-9]/', '', $searchTerm);
                    if ($normalizedSearch === '') {
                        return $query;
                    }
                    $dates = Withdrawal::query()
                        ->selectRaw('DATE(created_at) as datewd')
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->havingRaw('COUNT(id) like ?', ['%'.$normalizedSearch.'%'])
                        ->pluck('datewd');

                    return $query->whereIn(DB::raw('DATE(withdrawals.created_at)'), $dates);
                }),
            Filter::inputText('nominal_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }
                    $normalizedSearch = preg_replace('/[^0-9]/', '', $searchTerm);
                    if ($normalizedSearch === '') {
                        return $query;
                    }
                    $dates = Withdrawal::query()
                        ->selectRaw('DATE(created_at) as datewd')
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->havingRaw('SUM(nominal_receipt) like ?', ['%'.$normalizedSearch.'%'])
                        ->pluck('datewd');

                    return $query->whereIn(DB::raw('DATE(withdrawals.created_at)'), $dates);
                }),
            Filter::inputText('fee_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }
                    $normalizedSearch = preg_replace('/[^0-9]/', '', $searchTerm);
                    if ($normalizedSearch === '') {
                        return $query;
                    }
                    $dates = Withdrawal::query()
                        ->selectRaw('DATE(created_at) as datewd')
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->havingRaw('SUM(admin_fund) like ?', ['%'.$normalizedSearch.'%'])
                        ->pluck('datewd');

                    return $query->whereIn(DB::raw('DATE(withdrawals.created_at)'), $dates);
                }),
            Filter::inputText('total_transferred_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }
                    $normalizedSearch = preg_replace('/[^0-9]/', '', $searchTerm);
                    if ($normalizedSearch === '') {
                        return $query;
                    }
                    $dates = Withdrawal::query()
                        ->selectRaw('DATE(created_at) as datewd')
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->havingRaw('SUM(nominal_receipt + admin_fund) like ?', ['%'.$normalizedSearch.'%'])
                        ->pluck('datewd');

                    return $query->whereIn(DB::raw('DATE(withdrawals.created_at)'), $dates);
                }),
        ];
    }
}
