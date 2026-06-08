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
     * Auto-generate reward points upon order confirmation/completion.
     */
    protected static function booted(): void
    {
        static::saved(function (ProductOrder $order) {
            // Check if status is Confirmed (1) or Done (2)
            if (in_array((int) $order->status, [1, 2])) {
                // Determine if this is a transition to confirmed/done (either recently created, or status was 0/4)
                $wasConfirmed = $order->wasRecentlyCreated ? false : in_array((int) $order->getOriginal('status'), [1, 2]);
                if (! $wasConfirmed) {
                    // Prevent duplicate creation by checking if reward points already exist for this order
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
                }
            }
        });
    }
}
