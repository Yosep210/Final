<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberRewardPoint extends Model
{
    use HasFactory;

    protected $table = 'member_reward_points';

    protected $fillable = [
        'member_id',
        'type',
        'period',
        'period_start',
        'period_end',
        'point_left',
        'point_right',
        'point_qualified',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'point_left' => 'decimal:2',
        'point_right' => 'decimal:2',
        'point_qualified' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
