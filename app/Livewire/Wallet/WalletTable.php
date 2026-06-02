<?php

namespace App\Livewire\Wallet;

use App\Models\EwalletLog;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class WalletTable extends PowerGridComponent
{
    public string $tableName = 'walletTable';

    public string $sortField = 'ewallet_logs.created_at';

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
        return EwalletLog::query()
            ->join('members as member', 'ewallet_logs.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'ewallet_logs.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY ewallet_logs.created_at desc) AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (EwalletLog $row) => '<div><strong>'.e(strtoupper($row->member_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('type_formatted', function (EwalletLog $row) {
                $type = strtoupper($row->type);
                $class = $type === 'IN'
                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20'
                    : 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20';

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.$type.'</span>';
            })
            ->add('source', fn (EwalletLog $row) => $row->source ?: '-')
            ->add('category', fn (EwalletLog $row) => ucfirst($row->category ?? '-'))
            ->add('nominal_formatted', fn (EwalletLog $row) => number_format((float) ($row->nominal ?? 0), 0))
            ->add('tax_formatted', fn (EwalletLog $row) => number_format((float) ($row->tax ?? 0), 0))
            ->add('autoro_formatted', fn (EwalletLog $row) => number_format((float) ($row->autoro ?? 0), 0))
            ->add('amount_formatted', fn (EwalletLog $row) => number_format((float) ($row->amount ?? 0), 0))
            ->add('description', fn (EwalletLog $row) => $row->description ?: '-')
            ->add('created_at_formatted', fn (EwalletLog $row) => optional($row->created_at)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member'),
            Column::make('Type', 'type_formatted', 'type')->sortable(),
            Column::make('Source', 'source'),
            Column::make('Category', 'category'),
            Column::make('Nominal (Rp)', 'nominal_formatted', 'nominal')->sortable(),
            Column::make('Tax (Rp)', 'tax_formatted', 'tax')->sortable(),
            Column::make('Auto RO (Rp)', 'autoro_formatted', 'autoro')->sortable(),
            Column::make('Net Amount (Rp)', 'amount_formatted', 'amount')->sortable(),
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
            Filter::select('type', 'ewallet_logs.type')
                ->dataSource(collect([
                    ['id' => 'IN', 'name' => 'IN'],
                    ['id' => 'OUT', 'name' => 'OUT'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }
}
