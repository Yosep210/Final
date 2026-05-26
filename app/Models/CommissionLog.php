<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionLog extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'source',
        'left_volume',
        'right_volume',
        'matched_volume',
        'gross_commission',
        'tax_amount',
        'net_commission',
        'commission_rate',
        'tax_rate',
        'member_rank',
        'commission_year',
        'commission_month',
        'sponsored_by_id',
        'notes',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'left_volume' => 'decimal:2',
        'right_volume' => 'decimal:2',
        'matched_volume' => 'decimal:2',
        'gross_commission' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_commission' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'commission_year' => 'integer',
        'commission_month' => 'integer',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the member for this commission.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the sponsor member.
     */
    public function sponsoredBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'sponsored_by_id');
    }
}
