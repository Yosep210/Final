<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOrderDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'point' => 'decimal:2',
        'bv' => 'decimal:2',
        'omzet' => 'decimal:2',
        'price' => 'decimal:2',
        'price_cart' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'subtotal_bv' => 'decimal:2',
        'subtotal_omzet' => 'decimal:2',
        'subtotal_weight' => 'decimal:2',
        'subtotal_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order parent.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(ProductOrder::class, 'product_order_id');
    }

    /**
     * Get the member.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the package.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(ProductPackage::class, 'product_package_id');
    }
}
