<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoRoLog extends Model
{
    protected $fillable = [
        'member_id',
        'source_id',
        'source',
        'nominal',
        'percent',
        'amount',
        'status',
        'description',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'percent' => 'decimal:2',
        'amount' => 'decimal:2',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the member for this log.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
