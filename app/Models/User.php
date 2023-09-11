<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ladder\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function assignedCustomers():HasMany{
        return $this->hasMany(Customer::class, 'assigned_to');
    }

    /* public function adminRole(): HasOne{
        return $this->hasOne('user_roles')->latestOfMany();
    } */

    /* public function role(): HasOne{
        return $this->
    } */

    public function addedPurchases(): HasMany{
        return $this->hasMany(Purchase::class, 'added_by');
    }
}
