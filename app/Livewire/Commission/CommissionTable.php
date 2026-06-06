<?php

namespace App\Livewire\Commission;

use App\Models\CommissionLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CommissionTable extends PowerGridComponent
{
    public string $tableName = 'commissionTable';

    public string $sortField = 'total_gross_commission';

    public string $sortDirection = 'desc';

    public string $primaryKey = 'member_id';

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
            'total_gross_commission' => 'total_gross_commission',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'total_gross_commission';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $windowOrderMap = [
            'total_gross_commission' => 'SUM(commission_logs.gross_commission)',
        ];

        $windowOrder = $windowOrderMap[$sortColumn] ?? $sortColumn;

        return CommissionLog::query()
            ->join('members as member', 'commission_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'commission_logs.member_id',
                'member.name as member_name',
                'member.username as member_username',
                DB::raw('SUM(commission_logs.gross_commission) as total_gross_commission'),
            ])
            ->groupBy('commission_logs.member_id', 'member.name', 'member.username')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$windowOrder.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username', fn (CommissionLog $row) => strtoupper($row->member_username ?? ''))
            ->add('name', fn (CommissionLog $row) => $row->member_name)
            ->add('gross_commission_formatted', fn (CommissionLog $row) => number_format((float) ($row->total_gross_commission ?? 0), 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username', 'member.username')->sortable(),
            Column::make('Nama', 'name', 'member.name')->sortable(),
            Column::make('Jumlah (Rp)', 'gross_commission_formatted', 'total_gross_commission')->sortable(),
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
            Filter::inputText('gross_commission_formatted')
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

                    return $query->whereIn('commission_logs.member_id', function ($subQuery) use ($normalizedSearch) {
                        $subQuery->select('commission_logs.member_id')
                            ->from('commission_logs')
                            ->groupBy('commission_logs.member_id')
                            ->havingRaw('CAST(SUM(commission_logs.gross_commission) AS CHAR) like ?', ['%'.$normalizedSearch.'%']);
                    });
                }),
        ];
    }

    public function actions(CommissionLog $row): array
    {
        return [
            Button::add('view-detail')
                ->slot('Detail')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('commission:view-detail', ['memberId' => $row->member_id]),
        ];
    }
}
