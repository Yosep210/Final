<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class City extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'province_id',
        'name',
        'type',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'province_id' => 'integer',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'province_id',
                'name',
                'type',
            ])
            ->logOnlyDirty()
            ->useLogName('city');
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Backwards-compatible alias for legacy calls to `provincies`.
     */
    public function provincies()
    {
        return $this->belongsTo(Province::class);
    }
}
