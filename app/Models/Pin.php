<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pins';

    protected $fillable = [
        'serial_number',
        'pin_code',
        'status',
        'owner_id',
        'activated_member_id',
        'activated_at',
    ];

    protected $casts = [
        'owner_id' => 'integer',
        'activated_member_id' => 'integer',
        'activated_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'owner_id');
    }

    public function activatedMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'activated_member_id');
    }
}
