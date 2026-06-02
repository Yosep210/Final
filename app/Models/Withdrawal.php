<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'bank_name',
        'bank_code',
        'account_number',
        'account_holder',
        'nominal',
        'nominal_receipt',
        'tax',
        'auto_ro',
        'admin_fund',
        'status',
        'flip_id',
        'linkita_inquiry',
        'inquiry_status',
        'linkita_pay',
        'payment_status',
        'confirmed_at',
        'confirmed_by',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'nominal_receipt' => 'decimal:2',
        'tax' => 'decimal:2',
        'auto_ro' => 'decimal:2',
        'admin_fund' => 'decimal:2',
        'status' => 'integer',
        'confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the member for this withdrawal.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
