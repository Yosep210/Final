<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Country extends Model implements Auditable
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
        'iso',
        'name',
        'nice_name',
        'iso3',
        'numcode',
        'phonecode',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'numcode' => 'integer',
        'phonecode' => 'integer',
        'status' => 'boolean',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'iso',
                'name',
                'nice_name',
                'iso3',
                'numcode',
                'phonecode',
                'status',
            ])
            ->logOnlyDirty()
            ->useLogName('country');
    }
}
