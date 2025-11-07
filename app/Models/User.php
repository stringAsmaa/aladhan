<?php

namespace App\Models;

use App\Models\ZekrCategory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; // <--- هنا

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_token',
        'latitude',
        'longitude'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // JWTSubject methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return []; // يمكنك إضافة أي claim مخصص هنا
    }

    public function favoriteAzkar()
{
    return $this->belongsToMany(ZekrCategory::class, 'favorite_azkar')->withTimestamps();
}

}
