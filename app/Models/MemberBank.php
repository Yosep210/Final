<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberBank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'member_banks';

    protected $fillable = [
        'member_id',
        'bank_name',
        'account_number',
        'account_holder',
    ];

    protected $casts = [
        'member_id' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
