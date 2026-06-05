<?php

namespace App\Livewire\AutoRo;

use App\Models\AutoRoLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class AutoRoTable extends PowerGridComponent
{
    public string $tableName = 'autoRoTable';

    public string $sortField = 'total_amount';

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
            'total_amount' => 'total_amount',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'total_amount';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $windowOrderMap = [
            'total_amount' => 'SUM(auto_ro_logs.amount)',
        ];

        $windowOrder = $windowOrderMap[$sortColumn] ?? $sortColumn;

        return AutoRoLog::query()
            ->join('members as member', 'auto_ro_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'auto_ro_logs.member_id',
                'member.name as member_name',
                'member.username as member_username',
                DB::raw('SUM(auto_ro_logs.amount) as total_amount'),
            ])
            ->groupBy('auto_ro_logs.member_id', 'member.name', 'member.username')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$windowOrder.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username', fn (AutoRoLog $row) => strtoupper($row->member_username))
            ->add('name', fn (AutoRoLog $row) => $row->member_name)
            ->add('total_amount_formatted', fn (AutoRoLog $row) => number_format((float) ($row->total_amount ?? 0), 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username', 'member.username')->sortable(),
            Column::make('Nama', 'name', 'member.name')->sortable(),
            Column::make('Jumlah (Rp)', 'total_amount_formatted', 'total_amount')->sortable(),
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

    public function actions(AutoRoLog $row): array
    {
        return [
            Button::add('view-detail')
                ->slot('Detail')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('auto-ro:view-detail', ['memberId' => $row->member_id]),
        ];
    }
}
