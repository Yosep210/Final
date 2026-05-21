<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

#[Fillable(['member_id', 'gender', 'birth_date', 'phone', 'profile_photo', 'country_id', 'province_id', 'city_id', 'district_id', 'village_id', 'address'])]
#[Hidden(['id_card_number', 'id_card_photo', 'npwp_number'])]
class MemberProfile extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'member_profile';

    public function casts(): array
    {
        return [
            'member_id' => 'integer',
            'birth_date' => 'date',
            'country_id' => 'integer',
            'province_id' => 'integer',
            'city_id' => 'integer',
            'district_id' => 'integer',
            'village_id' => 'integer',
            'id_card_number' => 'string',
            'id_card_photo' => 'string',
            'npwp_number' => 'string',
            'phone' => 'string',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}
