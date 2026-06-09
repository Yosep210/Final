<?php

namespace App\Livewire\AutoRo;

use App\Models\AutoRoLog;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class HistoryTable extends PowerGridComponent
{
    public string $tableName = 'historyAutoRoTable';

    public string $sortField = 'created_at';

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
            'auto_ro_logs.created_at' => 'auto_ro_logs.created_at',
            'member.username' => 'member.username',
            'member.name' => 'member.name',
            'type' => 'type',
            'amount' => 'amount',
            'description' => 'description',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'auto_ro_logs.created_at';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $windowOrderMap = [
            'auto_ro_logs.created_at' => 'auto_ro_logs.created_at',
            'member.username' => 'member.username',
            'member.name' => 'member.name',
            'type' => 'auto_ro_logs.source',
            'amount' => 'auto_ro_logs.amount',
            'description' => 'auto_ro_logs.description',
        ];

        $windowOrder = $windowOrderMap[$sortField] ?? $sortField;

        return AutoRoLog::query()
            ->join('members as member', 'auto_ro_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'auto_ro_logs.*',
                'member.username as member_username',
                'member.name as member_name',
            ])
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
            ->add('date_formatted', fn (AutoRoLog $row) => $row->created_at?->format('Y-m-d @H:i') ?? '-')
            ->add('member_username_formatted', fn (AutoRoLog $row) => strtolower($row->member_username ?? ''))
            ->add('member_name_formatted', fn (AutoRoLog $row) => $row->member_name ?? '-')
            ->add('type', fn (AutoRoLog $row) => ((float) ($row->amount ?? 0)) >= 0 ? 'IN' : 'OUT')
            ->add('autoro_formatted', fn (AutoRoLog $row) => number_format(abs((float) ($row->amount ?? 0)), 0))
            ->add('status_label', fn (AutoRoLog $row) => $row->description ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Tanggal', 'date_formatted', 'created_at')->sortable(),
            Column::make('Username', 'member_username_formatted', 'member.username')->sortable(),
            Column::make('Nama', 'member_name_formatted', 'member.name')->sortable(),
            Column::make('Tipe', 'type', 'type')->sortable(),
            Column::make('Auto RO', 'autoro_formatted', 'nominal')->sortable(),
            Column::make('Keterangan', 'status_label', 'description')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('date_formatted', 'created_at'),
            Filter::inputText('member_username_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.username', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('member_name_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('type')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->whereRaw('CASE WHEN auto_ro_logs.nominal >= 0 THEN "IN" ELSE "OUT" END like ?', ['%'.$searchTerm.'%']);
                }),
            Filter::inputText('autoro_formatted')
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

                    return $query->whereRaw('CAST(ABS(auto_ro_logs.amount) AS CHAR) like ?', ['%'.$normalizedSearch.'%']);
                }),
            Filter::inputText('status_label')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('auto_ro_logs.description', 'like', '%'.$searchTerm.'%');
                }),
        ];
    }
}
