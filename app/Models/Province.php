<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Province extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use LogsActivity;

    protected $table = 'provinces';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_id',
        'name',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'country_id' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'country_id',
                'name',
            ])
            ->logOnlyDirty()
            ->useLogName('province');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
