<?php

namespace App\Livewire\AutoRo;

use App\Models\AutoRoLog;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class AutoRoTable extends PowerGridComponent
{
    public string $tableName = 'autoRoTable';

    public string $sortField = 'auto_ro_logs.created_at';

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
        return AutoRoLog::query()
            ->join('members as member', 'auto_ro_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'auto_ro_logs.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY auto_ro_logs.created_at desc) AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (AutoRoLog $row) => '<div><strong>'.e(strtoupper($row->member_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('source', fn (AutoRoLog $row) => ucfirst($row->source ?? '-'))
            ->add('nominal_formatted', fn (AutoRoLog $row) => number_format((float) ($row->nominal ?? 0), 0))
            ->add('percent_formatted', fn (AutoRoLog $row) => number_format((float) ($row->percent ?? 0), 0).'%')
            ->add('amount_formatted', fn (AutoRoLog $row) => number_format((float) ($row->amount ?? 0), 0))
            ->add('status_formatted', function (AutoRoLog $row) {
                $class = $row->status === 1
                    ? 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                    : 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20';
                $statusText = $row->status === 1 ? 'Active' : 'Processed';

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.$statusText.'</span>';
            })
            ->add('description', fn (AutoRoLog $row) => $row->description ?: '-')
            ->add('created_at_formatted', fn (AutoRoLog $row) => optional($row->created_at)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member'),
            Column::make('Source', 'source'),
            Column::make('Nominal (Rp)', 'nominal_formatted', 'nominal')->sortable(),
            Column::make('Percent', 'percent_formatted', 'percent')->sortable(),
            Column::make('Auto RO Amount (Rp)', 'amount_formatted', 'amount')->sortable(),
            Column::make('Status', 'status_formatted', 'status')->sortable(),
            Column::make('Description', 'description'),
            Column::make('Date', 'created_at_formatted'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('member')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('member.username', 'like', '%'.$searchTerm.'%');
                }),
        ];
    }
}
