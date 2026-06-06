<?php

namespace App\Livewire\Withdraw;

use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
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

    public string $primaryKey = 'id';

    public ?string $date = null;

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
            'withdrawals.type' => 'withdrawals.type',
            'withdrawals.bank_name' => 'withdrawals.bank_name',
            'withdrawals.account_number' => 'withdrawals.account_number',
            'withdrawals.nominal_receipt' => 'withdrawals.nominal_receipt',
            'withdrawals.status' => 'withdrawals.status',
            'withdrawals.created_at' => 'withdrawals.created_at',
            'withdrawals.confirmed_at' => 'withdrawals.confirmed_at',
            'withdrawals.confirmed_by' => 'withdrawals.confirmed_by',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'withdrawals.created_at';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Withdrawal::query()
            ->join('members as member', 'withdrawals.member_id', '=', 'member.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->when($this->date, function ($query) {
                $query->whereDate('withdrawals.created_at', $this->date);
            })
            ->select([
                'withdrawals.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortColumn.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (Withdrawal $row) => '<div><strong>'.e(strtoupper($row->member_username ?? '')).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('type_formatted', function (Withdrawal $row) {
                $type = strtolower($row->type ?: '');
                if ($type === 'ewallet') {
                    $class = 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20';
                    $text = 'e-Wallet';
                } elseif ($type === 'eproduct') {
                    $class = 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20';
                    $text = 'e-Product';
                } elseif ($type === 'eprofit') {
                    $class = 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20';
                    $text = 'e-Profit';
                } else {
                    $class = 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20';
                    $text = strtoupper($row->type ?: '-');
                }

                return '<span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset '.$class.'">'.$text.'</span>';
            })
            ->add('bank_formatted', fn (Withdrawal $row) => e(strtoupper($row->bank_code ? $row->bank_code.' - '.$row->bank_name : ($row->bank_name ?? ''))))
            ->add('rekening_formatted', fn (Withdrawal $row) => '<div><strong>No.Rek  : '.e($row->account_number).'</strong></div><div class="text-zinc-500 text-xs">An. Rek : '.e(strtoupper($row->account_holder ?? '')).'</div>')
            ->add('nominal_formatted', fn (Withdrawal $row) => number_format((float) ($row->nominal_receipt ?? 0), 0))
            ->add('status_formatted', function (Withdrawal $row) {
                $class = match ((int) $row->status) {
                    1 => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                    2 => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                    default => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                };
                $statusText = match ((int) $row->status) {
                    1 => 'TRANSFERED',
                    2 => 'PROCESSED',
                    default => 'PENDING',
                };

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset '.$class.'">'.$statusText.'</span>';
            })
            ->add('keterangan', function (Withdrawal $row) {
                $nominal = number_format((float) ($row->nominal ?? 0), 0);
                $fee = number_format((float) ($row->admin_fund ?? 0), 0);
                $tax = number_format((float) ($row->tax ?? 0), 0);
                $ro = number_format((float) ($row->auto_ro ?? 0), 0);

                $html = '<div class="text-[11px] font-mono space-y-0.5 leading-normal text-zinc-600 dark:text-zinc-400">';
                $html .= '<div>Withdrawal : <strong>Rp '.$nominal.'</strong></div>';
                if ($row->tax > 0) {
                    $html .= '<div>Pajak : <strong class="text-rose-500">-Rp '.$tax.'</strong></div>';
                }
                if ($row->auto_ro > 0) {
                    $html .= '<div>Auto-RO : <strong class="text-rose-500">-Rp '.$ro.'</strong></div>';
                }
                $html .= '<div>Fee : <strong class="text-rose-500">-Rp '.$fee.'</strong></div>';
                $html .= '</div>';

                return $html;
            })
            ->add('created_at_formatted', fn (Withdrawal $row) => $row->created_at?->locale('id')?->isoFormat('DD MMM YY HH:mm'))
            ->add('confirmed_at_formatted', fn (Withdrawal $row) => $row->confirmed_at?->locale('id')?->isoFormat('DD MMM YY HH:mm') ?: '-')
            ->add('confirmed_by_formatted', fn (Withdrawal $row) => $row->confirmed_by ?: '-');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member'),
            Column::make('Tipe', 'type_formatted', 'withdrawals.type')->sortable(),
            Column::make('Bank', 'bank_formatted', 'withdrawals.bank_name')->sortable(),
            Column::make('Rekening', 'rekening_formatted', 'withdrawals.account_number')->sortable(),
            Column::make('Nominal', 'nominal_formatted', 'withdrawals.nominal_receipt')->sortable(),
            Column::make('Status', 'status_formatted', 'withdrawals.status')->sortable(),
            Column::make('Keterangan', 'keterangan'),
            Column::make('Tanggal', 'created_at_formatted', 'withdrawals.created_at')->sortable(),
            Column::make('Tanggal Konfirmasi', 'confirmed_at_formatted', 'withdrawals.confirmed_at')->sortable(),
            Column::make('Dikonfirmasi Oleh', 'confirmed_by_formatted', 'withdrawals.confirmed_by')->sortable(),
            Column::action('Action'),
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

                    return $query->where(function ($q) use ($searchTerm) {
                        $q->where('member.name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('member.username', 'like', '%'.$searchTerm.'%');
                    });
                }),
            Filter::select('type_formatted')
                ->dataSource(collect([
                    ['id' => 'ewallet', 'name' => 'Wallet'],
                    ['id' => 'eproduct', 'name' => 'Product'],
                    ['id' => 'eprofit', 'name' => 'Profit'],
                ]))
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    if ($value === '' || $value === null) {
                        return $query;
                    }

                    return $query->where('withdrawals.type', $value);
                }),
            Filter::select('bank_formatted')
                ->dataSource(
                    Withdrawal::query()
                        ->select(['bank_name', 'bank_code'])
                        ->distinct()
                        ->whereNotNull('bank_name')
                        ->where('bank_name', '!=', '')
                        ->get()
                        ->map(function ($item) {
                            $name = $item->bank_code ? strtoupper($item->bank_code.' - '.$item->bank_name) : strtoupper($item->bank_name);

                            return [
                                'id' => $item->bank_name,
                                'name' => $name,
                            ];
                        })
                        ->sortBy('name')
                        ->values()
                )
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    if ($value === '' || $value === null) {
                        return $query;
                    }

                    return $query->where('withdrawals.bank_name', $value);
                }),
            Filter::inputText('rekening_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where(function ($q) use ($searchTerm) {
                        $q->where('withdrawals.account_number', 'like', '%'.$searchTerm.'%')
                            ->orWhere('withdrawals.account_holder', 'like', '%'.$searchTerm.'%');
                    });
                }),
            Filter::inputText('nominal_formatted')
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

                    return $query->where('withdrawals.nominal_receipt', 'like', '%'.$normalizedSearch.'%');
                }),
            Filter::select('status_formatted')
                ->dataSource(collect([
                    ['id' => 0, 'name' => 'Pending'],
                    ['id' => 1, 'name' => 'Transfered'],
                    ['id' => 2, 'name' => 'Processed'],
                ]))
                ->optionValue('id')
                ->optionLabel('name')
                ->builder(function (Builder $query, $value) {
                    if ($value === '' || $value === null) {
                        return $query;
                    }

                    return $query->where('withdrawals.status', $value);
                }),
            Filter::datepicker('created_at_formatted', 'withdrawals.created_at'),
            Filter::datepicker('confirmed_at_formatted', 'withdrawals.confirmed_at'),
            Filter::inputText('confirmed_by_formatted')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('withdrawals.confirmed_by', 'like', '%'.$searchTerm.'%');
                }),
        ];
    }

    public function actions(Withdrawal $row): array
    {
        if ((int) $row->status !== 0) {
            return [];
        }

        return [
            Button::add('confirm')
                ->slot('Confirm')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('withdraw:confirm', ['rowId' => $row->id]),
        ];
    }
}
