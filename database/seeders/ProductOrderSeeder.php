<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductOrderSeeder extends Seeder
{
    use HasSourceConnection;

    public function run(): void
    {
        $this->configureSourceConnection();

        $existingMemberIds = DB::table('members')->pluck('id')->all();
        $memberIdsMap = array_flip($existingMemberIds);

        $now = Carbon::now();

        // 1. Seed product_orders from jpb_shop_order
        $this->command?->info('Seeding product_orders...');
        $sourceOrders = DB::connection('latihan')
            ->table('jpb_shop_order')
            ->orderBy('id')
            ->get();

        foreach ($sourceOrders as $order) {
            $memberId = (int) $order->id_member;
            if (! isset($memberIdsMap[$memberId])) {
                continue;
            }

            $createdAt = $order->datecreated ? Carbon::parse($order->datecreated) : $now;
            $updatedAt = $order->datemodified ? Carbon::parse($order->datemodified) : $createdAt;

            DB::table('product_orders')->updateOrInsert(
                ['id' => $order->id],
                [
                    'id' => $order->id,
                    'invoice' => $order->invoice,
                    'member_id' => $memberId,
                    'stockist_id' => (int) $order->id_stockist,
                    'espay_id' => (int) $order->id_espay,
                    'type_order' => $order->type_order,
                    'products_json' => $order->products,
                    'meta_json' => $order->meta,
                    'status' => (int) $order->status,
                    'point_sponsor' => $order->point_sponsor,
                    'point_pairing' => $order->point_pairing,
                    'point_reward' => $order->point_reward,
                    'point_stockist' => $order->point_stockist,
                    'total_bv' => $order->total_bv,
                    'total_qty' => (int) $order->total_qty,
                    'subtotal' => $order->subtotal,
                    'shipping' => $order->shipping,
                    'unique_code' => (int) $order->unique,
                    'discount' => $order->discount,
                    'shipping_discount' => $order->shipping_discount,
                    'fee' => $order->fee,
                    'ppn' => $order->ppn,
                    'handling_fee' => $order->handling_fee,
                    'insurance_fee' => $order->insurance_fee,
                    'additional_cost' => $order->additional_cost,
                    'autoro' => $order->autoro,
                    'total_checkout' => $order->total_checkout,
                    'total_payment' => $order->total_payment,
                    'payment_remain' => (int) $order->payment_remain,
                    'total_omzet' => $order->total_omzet,
                    'voucher' => $order->voucher,
                    'saldo_eproduct' => $order->saldo_eproduct,
                    'saldo_eshipping' => $order->saldo_eshipping,
                    'saldo_eshipping_subsidy' => $order->saldo_eshipping_subsidy,
                    'payment_method' => $order->payment_method,
                    'payment_shipping_method' => $order->payment_shipping_method ?: 'transfer',
                    'payment_shipping_status' => (int) $order->payment_shipping_status,
                    'bank_code' => $order->bank_code,
                    'account_number' => $order->account_number,
                    'shipping_method' => $order->shipping_method,
                    'shipping_courier' => $order->courier,
                    'shipping_service' => $order->service,
                    'shipping_address' => $order->address,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }

        // 2. Seed product_order_details from jpb_shop_order_detail
        $this->command?->info('Seeding product_order_details...');
        $sourceDetails = DB::connection('latihan')
            ->table('jpb_shop_order_detail')
            ->orderBy('id')
            ->get();

        foreach ($sourceDetails as $detail) {
            $memberId = (int) $detail->id_member;
            if (! isset($memberIdsMap[$memberId])) {
                continue;
            }

            // Verify order exists in product_orders table
            $orderExists = DB::table('product_orders')->where('id', $detail->id_shop_order)->exists();
            if (! $orderExists) {
                continue;
            }

            $createdAt = $detail->datecreated ? Carbon::parse($detail->datecreated) : $now;
            $updatedAt = $detail->datemodified ? Carbon::parse($detail->datemodified) : $createdAt;

            DB::table('product_order_details')->updateOrInsert(
                ['id' => $detail->id],
                [
                    'id' => $detail->id,
                    'product_order_id' => (int) $detail->id_shop_order,
                    'member_id' => $memberId,
                    'product_package_id' => (int) $detail->product_package,
                    'product_id' => (int) $detail->product,
                    'varian_id' => (int) $detail->varian,
                    'type' => $detail->type,
                    'weight' => (int) $detail->weight,
                    'point' => $detail->point,
                    'bv' => $detail->bv,
                    'omzet' => $detail->omzet,
                    'price' => $detail->price,
                    'price_cart' => $detail->price_cart,
                    'additional_cost' => $detail->additional_cost,
                    'qty' => (int) $detail->qty,
                    'discount' => $detail->discount,
                    'subtotal' => $detail->subtotal,
                    'subtotal_bv' => $detail->subtotal_bv,
                    'subtotal_omzet' => $detail->subtotal_omzet,
                    'subtotal_weight' => $detail->subtotal_weight,
                    'subtotal_cost' => $detail->subtotal_cost,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }
    }
}
