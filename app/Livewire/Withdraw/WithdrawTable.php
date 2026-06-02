<?php

namespace App\Livewire\Withdraw;

use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class WithdrawTable extends PowerGridComponent
{
    public string $tableName = 'withdrawTable';

    public string $sortField = 'withdrawals.created_at';

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
        return Withdrawal::query()
            ->join('members as member', 'withdrawals.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'withdrawals.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY withdrawals.created_at desc) AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (Withdrawal $row) => '<div><strong>'.e(strtoupper($row->member_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('bank_info', function (Withdrawal $row) {
                $code = $row->bank_code ? ' ('.$row->bank_code.')' : '';

                return '<div><strong>'.e($row->bank_name).$code.'</strong></div><div class="text-zinc-500 text-xs">'.e($row->account_number).' a/n '.e($row->account_holder).'</div>';
            })
            ->add('nominal_formatted', fn (Withdrawal $row) => number_format((float) ($row->nominal ?? 0), 0))
            ->add('admin_fund_formatted', fn (Withdrawal $row) => number_format((float) ($row->admin_fund ?? 0), 0))
            ->add('tax_formatted', fn (Withdrawal $row) => number_format((float) ($row->tax ?? 0), 0))
            ->add('auto_ro_formatted', fn (Withdrawal $row) => number_format((float) ($row->auto_ro ?? 0), 0))
            ->add('nominal_receipt_formatted', fn (Withdrawal $row) => number_format((float) ($row->nominal_receipt ?? 0), 0))
            ->add('status_formatted', function (Withdrawal $row) {
                $class = match ((int) $row->status) {
                    1 => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                    2 => 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
                    default => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                };
                $statusText = match ((int) $row->status) {
                    1 => 'Success',
                    2 => 'Failed',
                    default => 'Pending',
                };

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.$statusText.'</span>';
            })
            ->add('created_at_formatted', fn (Withdrawal $row) => optional($row->created_at)->format('d M Y H:i'))
            ->add('confirm_info', function (Withdrawal $row) {
                if ($row->confirmed_at) {
                    return '<div>'.e(optional($row->confirmed_at)->format('d M Y H:i')).'</div><div class="text-xs text-zinc-500">By '.e($row->confirmed_by ?: 'System').'</div>';
                }

                return '-';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member'),
            Column::make('Bank Account', 'bank_info'),
            Column::make('Nominal (Rp)', 'nominal_formatted', 'nominal')->sortable(),
            Column::make('Admin Fee (Rp)', 'admin_fund_formatted', 'admin_fund')->sortable(),
            Column::make('Tax (Rp)', 'tax_formatted', 'tax')->sortable(),
            Column::make('Auto RO (Rp)', 'auto_ro_formatted', 'auto_ro')->sortable(),
            Column::make('Net Receipt (Rp)', 'nominal_receipt_formatted', 'nominal_receipt')->sortable(),
            Column::make('Status', 'status_formatted', 'status')->sortable(),
            Column::make('Requested Date', 'created_at_formatted'),
            Column::make('Confirmed Date', 'confirm_info'),
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
            Filter::select('status', 'withdrawals.status')
                ->dataSource(collect([
                    ['id' => 0, 'name' => 'Pending'],
                    ['id' => 1, 'name' => 'Success'],
                    ['id' => 2, 'name' => 'Failed'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }
}
