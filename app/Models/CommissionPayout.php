<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionPayout extends Model
{
    protected $fillable = [
        'member_id',
        'total_amount',
        'amount_paid',
        'amount_remaining',
        'payout_year',
        'payout_month',
        'status',
        'payment_method',
        'transaction_ref',
        'payout_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
        'payout_year' => 'integer',
        'payout_month' => 'integer',
        'payout_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the member for this payout.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get all commission logs for this payout.
     */
    public function commissionLogs()
    {
        return CommissionLog::where('member_id', $this->member_id)
            ->where('commission_year', $this->payout_year)
            ->where('commission_month', $this->payout_month)
            ->get();
    }
}
