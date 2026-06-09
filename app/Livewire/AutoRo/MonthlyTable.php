<?php

namespace App\Livewire\AutoRo;

use App\Models\AutoRoLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class MonthlyTable extends PowerGridComponent
{
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'monthlyAutoRoTable';

    public string $sortField = 'total_ro';

    public string $sortDirection = 'desc';

    public string $primaryKey = 'month_ro';

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
            'month_ro' => 'month_ro',
            'member.username' => 'member.username',
            'member.name' => 'member.name',
            'total_ro' => 'total_ro',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'total_ro';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $windowOrderMap = [
            'month_ro' => 'month_ro',
            'member.username' => 'member.username',
            'member.name' => 'member.name',
            'total_ro' => 'SUM(amount)',
        ];

        $windowOrder = $windowOrderMap[$sortField] ?? $sortField;

        return AutoRoLog::query()
            ->join('members as member', 'auto_ro_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'auto_ro_logs.member_id',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('DATE_FORMAT(auto_ro_logs.created_at, "%Y-%m-01") as month_ro')
            ->selectRaw('SUM(amount) as total_ro')
            ->groupBy('auto_ro_logs.member_id', 'member.name', 'member.username', 'month_ro')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$windowOrder.' '.$sortDirection.') AS no')
            ->orderBy($sortField, $sortDirection);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('month_label', fn (AutoRoLog $row) => Carbon::parse($row->month_ro)->translatedFormat('M, Y'))
            ->add('username', fn (AutoRoLog $row) => strtoupper($row->member_username ?? ''))
            ->add('name', fn (AutoRoLog $row) => $row->member_name ?? '-')
            ->add('total_ro_formatted', fn (AutoRoLog $row) => number_format((float) ($row->total_ro ?? 0), 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('MONTH', 'month_label', 'month_ro')->sortable(),
            Column::make('USERNAME', 'username', 'member.username')->sortable(),
            Column::make('NAMA', 'name', 'member.name')->sortable(),
            Column::make('JUMLAH', 'total_ro_formatted', 'total_ro')->sortable(),
            Column::action('PROSES')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('month_label', 'month_ro'),
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
            Filter::inputText('total_ro_formatted')
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

                    return $query->havingRaw('CAST(SUM(amount) AS CHAR) like ?', ['%'.$normalizedSearch.'%']);
                }),
        ];
    }

    public function actions(AutoRoLog $row): array
    {
        return [
            Button::add('view-detail')
                ->slot('Detail')
                ->class(self::BUTTON_CLASS)
                ->dispatch('auto-ro:view-month-detail', ['monthRo' => $row->month_ro]),
        ];
    }
}
