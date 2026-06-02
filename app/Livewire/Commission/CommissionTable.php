<?php

namespace App\Livewire\Commission;

use App\Models\CommissionLog;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CommissionTable extends PowerGridComponent
{
    public string $tableName = 'commissionTable';

    public string $sortField = 'commission_logs.created_at';

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
        return CommissionLog::query()
            ->join('members as member', 'commission_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'commission_logs.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY commission_logs.created_at desc) AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (CommissionLog $row) => '<div><strong>'.e(strtoupper($row->member_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('type_formatted', function (CommissionLog $row) {
                $class = match ($row->type) {
                    'sponsor' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                    'pairing' => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                    'unilevel' => 'bg-amber-50 text-amber-700 ring-amber-600/10 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
                    'generation' => 'bg-purple-50 text-purple-700 ring-purple-600/10 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20',
                    default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20',
                };

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.ucfirst($row->type).'</span>';
            })
            ->add('source', fn (CommissionLog $row) => $row->source ?: '-')
            ->add('gross_commission_formatted', fn (CommissionLog $row) => number_format((float) ($row->gross_commission ?? 0), 0))
            ->add('tax_amount_formatted', fn (CommissionLog $row) => number_format((float) ($row->tax_amount ?? 0), 0))
            ->add('net_commission_formatted', fn (CommissionLog $row) => number_format((float) ($row->net_commission ?? 0), 0))
            ->add('status_formatted', function (CommissionLog $row) {
                $statusText = $row->is_paid ? 'Paid' : 'Unpaid';
                $class = $row->is_paid
                    ? 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                    : 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20';

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.$statusText.'</span>';
            })
            ->add('created_at_formatted', fn (CommissionLog $row) => optional($row->created_at)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member'),
            Column::make('Type', 'type_formatted', 'type')->sortable(),
            Column::make('Source ID', 'source'),
            Column::make('Gross (Rp)', 'gross_commission_formatted', 'gross_commission')->sortable(),
            Column::make('Tax (Rp)', 'tax_amount_formatted', 'tax_amount')->sortable(),
            Column::make('Net (Rp)', 'net_commission_formatted', 'net_commission')->sortable(),
            Column::make('Status', 'status_formatted', 'is_paid')->sortable(),
            Column::make('Date Created', 'created_at_formatted'),
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
            Filter::select('type', 'commission_logs.type')
                ->dataSource(collect([
                    ['id' => 'sponsor', 'name' => 'Sponsor'],
                    ['id' => 'pairing', 'name' => 'Pairing'],
                    ['id' => 'unilevel', 'name' => 'Unilevel'],
                    ['id' => 'generation', 'name' => 'Generation'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::select('status', 'commission_logs.is_paid')
                ->dataSource(collect([
                    ['id' => 1, 'name' => 'Paid'],
                    ['id' => 0, 'name' => 'Unpaid'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }
}
