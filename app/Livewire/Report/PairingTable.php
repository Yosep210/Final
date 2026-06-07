<?php

namespace App\Livewire\Report;

use App\Models\CommissionLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class PairingTable extends PowerGridComponent
{
    public string $tableName = 'pairingTable';

    public string $sortField = 'total_qualified';

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
            'total_qualified' => 'total_qualified',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'total_qualified';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $windowOrderMap = [
            'total_qualified' => 'SUM(commission_logs.matched_volume)',
        ];
        $windowOrder = $windowOrderMap[$sortColumn] ?? $sortColumn;

        return CommissionLog::query()
            ->join('members as member', 'commission_logs.member_id', '=', 'member.id')
            ->where('commission_logs.type', 'pairing')
            ->select([
                'commission_logs.member_id',
                'member.username as member_username',
                'member.name as member_name',
                DB::raw('SUM(commission_logs.matched_volume) as total_qualified')
            ])
            ->groupBy('commission_logs.member_id', 'member.username', 'member.name')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$windowOrder.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username', fn (CommissionLog $row) => strtoupper($row->member_username ?? ''))
            ->add('name', fn (CommissionLog $row) => $row->member_name)
            ->add('total_qualified_formatted', fn (CommissionLog $row) => number_format((float)$row->total_qualified, 0) . ' BV');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username', 'member.username')->sortable(),
            Column::make('Nama Member', 'name', 'member.name')->sortable(),
            Column::make('Total Pairing Qualified', 'total_qualified_formatted', 'total_qualified')->sortable(),
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
        ];
    }

    public function actions(CommissionLog $row): array
    {
        return [
            Button::add('view-detail')
                ->slot('Detail')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('pairing:view-detail', ['memberId' => $row->member_id]),
        ];
    }
}
