<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

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

    protected $casts = ['status' => 'boolean'];
}
