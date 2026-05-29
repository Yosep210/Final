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
        'left_volume',
        'right_volume',
        'total_volume',
        'qualified_legs',
        'current_rank',
    ];

    protected $casts = [
        'qualified_legs' => 'integer',
    ];

    protected function normalizeNumeric(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) str_replace([',', ' '], '', (string) $value);
    }

    public function getLeftVolumeAttribute(mixed $value): float
    {
        return $this->normalizeNumeric($value);
    }

    public function getRightVolumeAttribute(mixed $value): float
    {
        return $this->normalizeNumeric($value);
    }

    public function getTotalVolumeAttribute(mixed $value): float
    {
        return $this->normalizeNumeric($value);
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(Member::class, 'sponsored_id');
    }

    public function parent()
    {
        return $this->belongsTo(Member::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
