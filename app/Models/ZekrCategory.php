<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZekrCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * العلاقة: كل نوع ذكر يحتوي على عدة أذكار.
     */
    public function azkar()
    {
        return $this->hasMany(Zekr::class);
    }
    public function favoritedByUsers()
{
    return $this->belongsToMany(User::class, 'favorite_azkar')->withTimestamps();
}

}
