<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reward extends Model
{
    use HasFactory;

    protected $table = 'rewards';

    protected $fillable = [
        'member_id',
        'reward_config_id',
        'type',
        'point_qualified',
        'point_left',
        'point_right',
        'rank',
        'message',
        'nominal',
        'nominal_receipt',
        'admin_fund',
        'tax',
        'bank_name',
        'bank_code',
        'account_number',
        'account_holder',
        'is_trip',
        'claim',
        'flip_id',
        'status',
        'inquiry_status',
        'confirmed_at',
        'claimed_at',
        'confirm_by',
    ];

    protected $casts = [
        'point_qualified' => 'decimal:2',
        'point_left' => 'decimal:2',
        'point_right' => 'decimal:2',
        'nominal' => 'decimal:2',
        'nominal_receipt' => 'decimal:2',
        'admin_fund' => 'decimal:2',
        'tax' => 'decimal:2',
        'is_trip' => 'boolean',
        'claim' => 'boolean',
        'confirmed_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function rewardConfig(): BelongsTo
    {
        return $this->belongsTo(RewardConfig::class, 'reward_config_id');
    }
}
