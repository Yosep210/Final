<?php

namespace App\Livewire\Commission;

use App\Models\CommissionPayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class StatementTable extends PowerGridComponent
{
    public string $tableName = 'statementTable';

    public string $sortField = 'commission_payouts.payout_year';

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
        return CommissionPayout::query()
            ->join('members as member', 'commission_payouts.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'commission_payouts.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY commission_payouts.payout_year desc, commission_payouts.payout_month desc) AS no')
            ->orderBy('commission_payouts.payout_year', 'desc')
            ->orderBy('commission_payouts.payout_month', 'desc');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (CommissionPayout $row) => '<div><strong>'.e(strtoupper($row->member_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('period', fn (CommissionPayout $row) => Carbon::create($row->payout_year, $row->payout_month, 1)->format('F Y'))
            ->add('total_amount_formatted', fn (CommissionPayout $row) => number_format((float) ($row->total_amount ?? 0), 0))
            ->add('amount_paid_formatted', fn (CommissionPayout $row) => number_format((float) ($row->amount_paid ?? 0), 0))
            ->add('amount_remaining_formatted', fn (CommissionPayout $row) => number_format((float) ($row->amount_remaining ?? 0), 0))
            ->add('status_formatted', function (CommissionPayout $row) {
                $class = match ($row->status) {
                    'completed' => 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20',
                    'partial' => 'bg-amber-50 text-amber-700 ring-amber-600/10 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
                    'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                    default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20',
                };

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.ucfirst($row->status).'</span>';
            })
            ->add('transaction_ref', fn (CommissionPayout $row) => $row->transaction_ref ?: '-')
            ->add('payout_date_formatted', fn (CommissionPayout $row) => $row->payout_date?->locale('id')?->isoFormat('DD MMM YY HH:mm') ?: '-');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member'),
            Column::make('Period', 'period'),
            Column::make('Total Gross (Rp)', 'total_amount_formatted', 'total_amount')->sortable(),
            Column::make('Amount Paid (Rp)', 'amount_paid_formatted', 'amount_paid')->sortable(),
            Column::make('Amount Remaining (Rp)', 'amount_remaining_formatted', 'amount_remaining')->sortable(),
            Column::make('Status', 'status_formatted', 'status')->sortable(),
            Column::make('Transaction Ref', 'transaction_ref'),
            Column::make('Payout Date', 'payout_date_formatted'),
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
            Filter::select('status', 'commission_payouts.status')
                ->dataSource(collect([
                    ['id' => 'completed', 'name' => 'Completed'],
                    ['id' => 'pending', 'name' => 'Pending'],
                    ['id' => 'partial', 'name' => 'Partial'],
                    ['id' => 'cancelled', 'name' => 'Cancelled'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }
}
