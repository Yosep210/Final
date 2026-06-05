<?php

namespace App\Livewire\Wallet;

use App\Models\EwalletLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class WalletTable extends PowerGridComponent
{
    public string $tableName = 'walletTable';

    public string $sortField = 'total_balance';

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
            'member.username' => 'member.username',
            'member.name' => 'member.name',
            'member.status' => 'member.status',
            'total_balance' => 'total_balance',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'total_balance';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $windowOrderMap = [
            'total_balance' => "SUM(CASE WHEN ewallet_logs.type = 'IN' THEN ewallet_logs.amount ELSE 0 END) - SUM(CASE WHEN ewallet_logs.type = 'OUT' THEN ewallet_logs.amount ELSE 0 END)",
        ];

        $windowOrder = $windowOrderMap[$sortColumn] ?? $sortColumn;

        return EwalletLog::query()
            ->join('members as member', 'ewallet_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'ewallet_logs.member_id',
                'member.name as member_name',
                'member.username as member_username',
                'member.status as member_status',
                DB::raw("SUM(CASE WHEN ewallet_logs.type = 'IN' THEN ewallet_logs.amount ELSE 0 END) - SUM(CASE WHEN ewallet_logs.type = 'OUT' THEN ewallet_logs.amount ELSE 0 END) as total_balance"),
            ])
            ->groupBy('ewallet_logs.member_id', 'member.name', 'member.username', 'member.status')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$windowOrder.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username', fn (EwalletLog $row) => strtoupper($row->member_username))
            ->add('name', fn (EwalletLog $row) => $row->member_name)
            ->add('status', function (EwalletLog $row) {
                $status = strtolower($row->member_status ?: 'active');
                $class = match ($status) {
                    'active' => 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20',
                    'suspended' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                    'inactive' => 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20',
                    default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20',
                };

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.ucfirst($status).'</span>';
            })
            ->add('total_balance_formatted', fn (EwalletLog $row) => number_format((float) ($row->total_balance ?? 0), 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username', 'member.username')->sortable(),
            Column::make('Nama', 'name', 'member.name')->sortable(),
            Column::make('Status', 'status', 'member.status')->sortable(),
            Column::make('Jumlah (Rp)', 'total_balance_formatted', 'total_balance')->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('username')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.username', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('status', 'member.status')
                ->dataSource(collect([
                    ['id' => 'active', 'name' => 'Active'],
                    ['id' => 'suspended', 'name' => 'Suspended'],
                    ['id' => 'inactive', 'name' => 'Inactive'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }

    public function actions(EwalletLog $row): array
    {
        return [
            Button::add('view-detail')
                ->slot('Detail')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('wallet:view-detail', ['memberId' => $row->member_id]),
        ];
    }
}
