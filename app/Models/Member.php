<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class Member extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasPermissions, HasRoles, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'status',
        'referral_code',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'referral_code',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(MemberProfile::class, 'member_id');
    }

    /**
     * Member's network record
     */
    public function network(): HasOne
    {
        return $this->hasOne(MemberNetwork::class, 'member_id');
    }

    /**
     * Member's bank account details
     */
    public function bank(): HasOne
    {
        return $this->hasOne(MemberBank::class, 'member_id');
    }

    /**
     * PINs owned by this member
     */
    public function pins()
    {
        return $this->hasMany(Pin::class, 'owner_id');
    }

    /**
     * PIN used to activate this member
     */
    public function activationPin(): HasOne
    {
        return $this->hasOne(Pin::class, 'activated_member_id');
    }

    /**
     * Networks where this member is the sponsor
     */
    public function sponsoredNetworks()
    {
        return $this->hasMany(MemberNetwork::class, 'sponsored_id');
    }

    /**
     * Get the member's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
