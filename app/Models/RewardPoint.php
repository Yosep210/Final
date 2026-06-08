<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardPoint extends Model
{
    use HasFactory;

    protected $table = 'reward_points';

    protected $fillable = [
        'member_id',
        'product_order_id',
        'package',
        'type',
        'bv',
        'point',
        'status',
    ];

    protected $casts = [
        'point' => 'decimal:2',
        'bv' => 'integer',
        'status' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function productOrder(): BelongsTo
    {
        return $this->belongsTo(ProductOrder::class, 'product_order_id');
    }
}
