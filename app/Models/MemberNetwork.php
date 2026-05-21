<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberNetwork extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'member_networks';

    protected $fillable = [
        'member_id',
        'sponsored_id',
        'parent_id',
        'position',
        'path',
        'generation',
        'group',
        'rank',
    ];
}
