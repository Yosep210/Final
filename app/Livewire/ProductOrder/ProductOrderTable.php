<?php

namespace App\Livewire\ProductOrder;

use App\Models\ProductOrder;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ProductOrderTable extends PowerGridComponent
{
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'productOrderTable';

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
            'product_orders.type_order' => 'product_orders.type_order',
            'product_orders.status' => 'product_orders.status',
            'product_orders.total_checkout' => 'product_orders.total_checkout',
            'product_orders.payment_method' => 'product_orders.payment_method',
            'product_orders.shipping_method' => 'product_orders.shipping_method',
            'product_orders.created_at' => 'product_orders.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'product_orders.created_at';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return ProductOrder::query()
            ->with(['details.product', 'details.package'])
            ->join('members as member', 'product_orders.member_id', '=', 'member.id')
            ->select([
                'product_orders.*',
                'member.name as member_name',
                'member.username as member_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('invoice', fn (ProductOrder $row) => '<strong>'.e($row->invoice).'</strong>')
            ->add('member', fn (ProductOrder $row) => '<div><strong>'.e(strtoupper($row->member_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('type_badge', function (ProductOrder $row) {
                $type = $row->type_order ?: 'unknown';
                $class = match ($type) {
                    'generate', 'stockist' => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-950/30 dark:text-yellow-400 dark:ring-yellow-500/20',
                    'register', 'activation' => 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-950/30 dark:text-blue-400 dark:ring-blue-500/20',
                    'manual_ro', 'ro' => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10 dark:bg-indigo-950/30 dark:text-indigo-400 dark:ring-indigo-500/20',
                    default => 'bg-zinc-50 text-zinc-600 ring-zinc-500/10 dark:bg-zinc-950/30 dark:text-zinc-400 dark:ring-zinc-500/20',
                };

                return '<span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset '.$class.'">'.strtoupper(str_replace('_', ' ', $type)).'</span>';
            })
            ->add('product_list', function (ProductOrder $row) {
                $parts = [];
                foreach ($row->details as $detail) {
                    if ($detail->package) {
                        $parts[] = '- '.e($detail->package->name).' [<strong>'.$detail->qty.'</strong> qty]';
                    } elseif ($detail->product) {
                        $parts[] = '- '.e($detail->product->name).' [<strong>'.$detail->qty.'</strong> qty]';
                    } else {
                        $parts[] = '- '.e(ucfirst($detail->type ?: 'Item')).' [<strong>'.$detail->qty.'</strong> qty]';
                    }
                }

                return ! empty($parts) ? '<div class="space-y-1 text-xs font-mono text-left leading-normal">'.implode('<br>', $parts).'</div>' : '<span class="text-zinc-400 text-xs">-</span>';
            })
            ->add('total_checkout_formatted', fn (ProductOrder $row) => number_format($row->total_checkout, 0))
            ->add('payment_method_badge', function (ProductOrder $row) {
                $pm = $row->payment_method ?: 'unknown';
                $class = match ($pm) {
                    'autoro' => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10 dark:bg-indigo-950/30 dark:text-indigo-400 dark:ring-indigo-500/20',
                    'wallet', 'eproduct' => 'bg-sky-50 text-sky-700 ring-sky-700/10 dark:bg-sky-950/30 dark:text-sky-400 dark:ring-sky-500/20',
                    'transfer' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                    default => 'bg-zinc-50 text-zinc-600 ring-zinc-500/10 dark:bg-zinc-950/30 dark:text-zinc-400 dark:ring-zinc-500/20',
                };

                return '<span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset '.$class.'">'.strtoupper(str_replace('_', ' ', $pm)).'</span>';
            })
            ->add('shipping_info', function (ProductOrder $row) {
                if (strtolower($row->shipping_method) === 'pickup') {
                    return '<div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">PICKUP</div>';
                }
                $parts = ['<div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">EKSPEDISI</div>'];
                $courierAndService = [];
                if ($row->shipping_courier) {
                    $courierAndService[] = strtoupper($row->shipping_courier);
                }
                if ($row->shipping_service) {
                    $courierAndService[] = strtoupper($row->shipping_service);
                }
                if (! empty($courierAndService)) {
                    $parts[] = '<div class="text-zinc-500 text-xs mt-0.5">'.e(implode(' - ', $courierAndService)).'</div>';
                }

                return implode('', $parts);
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
            ->add('created_at_formatted', fn (ProductOrder $row) => optional($row->created_at)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Invoice', 'invoice', 'product_orders.invoice')->sortable(),
            Column::make('Member', 'member', 'member.name')->sortable(),
            Column::make('Type', 'type_badge', 'product_orders.type_order')->sortable(),
            Column::make('Product', 'product_list'),
            Column::make('Total Payment (Rp)', 'total_checkout_formatted', 'product_orders.total_checkout')->sortable(),
            Column::make('Payment Method', 'payment_method_badge', 'product_orders.payment_method')->sortable(),
            Column::make('Shipping Method', 'shipping_info', 'product_orders.shipping_method')->sortable(),
            Column::make('Status', 'status_badge', 'product_orders.status')->sortable(),
            Column::make('Order Date', 'created_at_formatted', 'product_orders.created_at')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
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

    public function actions(ProductOrder $row): array
    {
        return [
            Button::add('view')
                ->slot('Detail')
                ->class(self::BUTTON_CLASS)
                ->dispatch('product-order:view-detail', ['orderId' => $row->id]),
        ];
    }
}
