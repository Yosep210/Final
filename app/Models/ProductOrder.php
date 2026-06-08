<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'point_sponsor' => 'decimal:2',
        'point_pairing' => 'decimal:2',
        'point_reward' => 'decimal:2',
        'point_stockist' => 'decimal:2',
        'total_bv' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_discount' => 'decimal:2',
        'fee' => 'decimal:2',
        'ppn' => 'decimal:2',
        'handling_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'autoro' => 'decimal:2',
        'total_checkout' => 'decimal:2',
        'total_payment' => 'decimal:2',
        'total_omzet' => 'decimal:2',
        'voucher' => 'decimal:2',
        'saldo_eproduct' => 'decimal:2',
        'saldo_eshipping' => 'decimal:2',
        'saldo_eshipping_subsidy' => 'decimal:2',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the member who placed the order.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the details for this order.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProductOrderDetail::class);
    }

    /**
     * Auto-generate reward points and update stockist inventory upon order confirmation/completion or cancellation.
     */
    protected static function booted(): void
    {
        static::saved(function (ProductOrder $order) {
            // Check if status is Confirmed (1) or Done (2)
            if (in_array((int) $order->status, [1, 2])) {
                // Determine if this is a transition to confirmed/done (either recently created, or status was 0/4)
                $wasConfirmed = $order->wasRecentlyCreated ? false : in_array((int) $order->getOriginal('status'), [1, 2]);
                if (! $wasConfirmed) {
                    // 1. Reward points generation
                    if (! RewardPoint::where('product_order_id', $order->id)->exists()) {
                        // Determine type: 'activation' or 'ro'
                        $type = 'ro';
                        if (in_array(strtolower($order->type_order), ['register', 'activation'])) {
                            $type = 'activation';
                        }

                        // Determine package code
                        $packageCode = 'silver';
                        $firstDetail = $order->details()->with('package')->first();
                        if ($firstDetail && $firstDetail->package) {
                            $packageCode = $firstDetail->package->sku ?: $firstDetail->package->name;
                        }

                        // Create RewardPoint log
                        RewardPoint::create([
                            'member_id' => $order->member_id,
                            'product_order_id' => $order->id,
                            'package' => $packageCode,
                            'type' => $type,
                            'bv' => (int) ($order->total_bv ?: 0),
                            'point' => (float) ($order->point_reward ?: 0.0),
                            'status' => 1,
                        ]);
                    }

                    // 2. Stockist stock updates
                    // Check if stock records already exist to prevent duplicate execution
                    $stockExists = ProductStockist::where('id_source', $order->id)
                        ->where('source', 'shop_order')
                        ->exists();

                    if (! $stockExists) {
                        $cart = json_decode($order->products_json, true) ?: [];

                        // Case A: served by stockist -> deduct stockist stock
                        if ($order->stockist_id > 0) {
                            foreach ($cart as $item) {
                                ProductStockist::create([
                                    'id_member' => $order->stockist_id,
                                    'id_source' => $order->id,
                                    'source' => 'shop_order',
                                    'product' => $item['product_id'],
                                    'varian' => $item['variant'] ?? null,
                                    'qty' => $item['qty'],
                                    'price' => $item['price'],
                                    'total' => $item['price'] * $item['qty'],
                                    'type' => 'OUT',
                                    'status' => 1,
                                    'description' => 'Stok Keluar dari Pesanan #'.$order->invoice,
                                ]);
                            }
                        }

                        // Case B: stockist buying from Pusat -> add stockist stock
                        if ($order->stockist_id === 0 && $order->type_order === 'stockist') {
                            foreach ($cart as $item) {
                                ProductStockist::create([
                                    'id_member' => $order->member_id,
                                    'id_source' => $order->id,
                                    'source' => 'shop_order',
                                    'product' => $item['product_id'],
                                    'varian' => $item['variant'] ?? null,
                                    'qty' => $item['qty'],
                                    'price' => $item['price'],
                                    'total' => $item['price'] * $item['qty'],
                                    'type' => 'IN',
                                    'status' => 1,
                                    'description' => 'Stok Masuk dari Pesanan #'.$order->invoice,
                                ]);
                            }
                        }
                    }
                }
            }

            // Check if status is Cancelled (4)
            if ((int) $order->status === 4) {
                // Check if this is a transition to cancelled (original status was 0, 1, or 2)
                $wasCancelled = $order->wasRecentlyCreated ? false : (int) $order->getOriginal('status') === 4;
                if (! $wasCancelled) {
                    $cart = json_decode($order->products_json, true) ?: [];

                    // Revert Case A: served by stockist -> refund stockist stock (type = IN)
                    if ($order->stockist_id > 0) {
                        $refundExists = ProductStockist::where('id_source', $order->id)
                            ->where('source', 'shop_order')
                            ->where('type', 'IN')
                            ->where('description', 'like', 'Refund Stok%')
                            ->exists();

                        if (! $refundExists) {
                            foreach ($cart as $item) {
                                ProductStockist::create([
                                    'id_member' => $order->stockist_id,
                                    'id_source' => $order->id,
                                    'source' => 'shop_order',
                                    'product' => $item['product_id'],
                                    'varian' => $item['variant'] ?? null,
                                    'qty' => $item['qty'],
                                    'price' => $item['price'],
                                    'total' => $item['price'] * $item['qty'],
                                    'type' => 'IN',
                                    'status' => 1,
                                    'description' => 'Refund Stok dari Pembatalan Pesanan #'.$order->invoice,
                                ]);
                            }
                        }
                    }

                    // Revert Case B: stockist buying from Pusat -> deduct stockist stock (type = OUT)
                    if ($order->stockist_id === 0 && $order->type_order === 'stockist') {
                        $reversalExists = ProductStockist::where('id_source', $order->id)
                            ->where('source', 'shop_order')
                            ->where('type', 'OUT')
                            ->where('description', 'like', 'Pembatalan Stok%')
                            ->exists();

                        if (! $reversalExists) {
                            foreach ($cart as $item) {
                                ProductStockist::create([
                                    'id_member' => $order->member_id,
                                    'id_source' => $order->id,
                                    'source' => 'shop_order',
                                    'product' => $item['product_id'],
                                    'varian' => $item['variant'] ?? null,
                                    'qty' => $item['qty'],
                                    'price' => $item['price'],
                                    'total' => $item['price'] * $item['qty'],
                                    'type' => 'OUT',
                                    'status' => 1,
                                    'description' => 'Pembatalan Stok dari Pesanan #'.$order->invoice,
                                ]);
                            }
                        }
                    }
                }
            }
        });
    }
}
