<?php

namespace App\Livewire\Report;

use App\Models\ProductOrder;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class RegistrationTable extends PowerGridComponent
{
    public string $tableName = 'registrationTable';

    public string $sortField = 'product_orders.created_at';

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
            'product_orders.invoice' => 'product_orders.invoice',
            'member.name' => 'member.name',
            'product_orders.total_checkout' => 'product_orders.total_checkout',
            'product_orders.payment_method' => 'product_orders.payment_method',
            'product_orders.status' => 'product_orders.status',
            'product_orders.created_at' => 'product_orders.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'product_orders.created_at';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return ProductOrder::query()
            ->join('members as member', 'product_orders.member_id', '=', 'member.id')
            ->where('product_orders.type_order', 'register')
            ->select([
                'product_orders.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no')
            ->orderBy($sortField, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('invoice', fn (ProductOrder $row) => '<strong>'.e($row->invoice).'</strong>')
            ->add('member', fn (ProductOrder $row) => '<div><strong>'.e(strtoupper($row->member_username ?? '')).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('total_checkout_formatted', fn (ProductOrder $row) => 'Rp '.number_format($row->total_checkout, 0))
            ->add('payment_method_badge', function (ProductOrder $row) {
                $pm = $row->payment_method ?: 'unknown';

                return '<span class="text-xs uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded px-2 py-0.5 font-semibold">'.str_replace('_', ' ', $pm).'</span>';
            })
            ->add('status_badge', function (ProductOrder $row) {
                $class = match ((int) $row->status) {
                    1 => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                    2 => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                    4 => 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
                    default => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                };

                $statusText = match ((int) $row->status) {
                    0 => 'Review',
                    1 => 'Confirmed',
                    2 => 'Done',
                    4 => 'Cancelled',
                    default => 'Unknown',
                };

                return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.$statusText.'</span>';
            })
            ->add('created_at_formatted', fn (ProductOrder $row) => $row->created_at?->locale('id')?->isoFormat('DD MMM YYYY HH:mm'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Tanggal Daftar', 'created_at_formatted', 'product_orders.created_at')->sortable(),
            Column::make('Invoice', 'invoice', 'product_orders.invoice')->sortable(),
            Column::make('Member', 'member', 'member.name')->sortable(),
            Column::make('Total Payment', 'total_checkout_formatted', 'product_orders.total_checkout')->sortable(),
            Column::make('Payment Method', 'payment_method_badge', 'product_orders.payment_method')->sortable(),
            Column::make('Status', 'status_badge', 'product_orders.status')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('invoice')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('product_orders.invoice', 'like', '%'.$searchTerm.'%');
                }),
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
            Filter::select('status_badge', 'product_orders.status')
                ->dataSource(collect([
                    ['id' => 0, 'name' => 'Review'],
                    ['id' => 1, 'name' => 'Confirmed'],
                    ['id' => 2, 'name' => 'Done'],
                    ['id' => 4, 'name' => 'Cancelled'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }
}
