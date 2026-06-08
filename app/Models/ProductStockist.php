<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockist extends Model
{
    use HasFactory;

    protected $table = 'product_stockist';

    protected $guarded = [];

    protected $casts = [
        'id_member' => 'integer',
        'id_source' => 'integer',
        'id_detail' => 'integer',
        'product' => 'integer',
        'qty' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => 'integer',
        'datecreated' => 'datetime',
    ];

    /**
     * Get the member who owns this stock.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    /**
     * Get the product.
     */
    public function productModel(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product');
    }

    /**
     * Get the current stock count of a specific product and variant for a stockist.
     */
    public static function getStock(int $memberId, int $productId, ?string $variant = null): int
    {
        $queryIn = self::where('id_member', $memberId)
            ->where('product', $productId)
            ->where('type', 'IN');

        $queryOut = self::where('id_member', $memberId)
            ->where('product', $productId)
            ->where('type', 'OUT');

        if (! is_null($variant) && $variant !== '') {
            $queryIn->where('varian', $variant);
            $queryOut->where('varian', $variant);
        }

        $in = (int) $queryIn->sum('qty');
        $out = (int) $queryOut->sum('qty');

        return max(0, $in - $out);
    }
}
