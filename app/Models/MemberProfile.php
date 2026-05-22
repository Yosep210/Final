<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'member_profile';

    protected $fillable = [
        'member_id',
        'gender',
        'birth_date',
        'phone',
        'profile_photo',
        'country_id',
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'address',
        'id_card_number',
        'id_card_photo',
        'npwp_number',
    ];

    protected $hidden = [
        'id_card_number',
        'id_card_photo',
        'npwp_number',
    ];

    protected $casts = [
        'member_id' => 'integer',
        'birth_date' => 'date',
        'country_id' => 'integer',
        'province_id' => 'integer',
        'city_id' => 'integer',
        'district_id' => 'integer',
        'village_id' => 'integer',
        'phone' => 'string',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}
