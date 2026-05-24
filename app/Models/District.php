<?php

namespace App\Models;

use App\Concerns\HasDisplayName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class District extends Model implements Auditable
{
    use AuditableTrait;
    use HasDisplayName;
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'city_id',
    ];

    protected $casts = [
        'city_id' => 'integer',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function villages()
    {
        return $this->hasMany(Village::class);
    }

    /**
     * Get the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'city_id',
            ])
            ->logOnlyDirty()
            ->useLogName('district');
    }
}
