<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserZekrLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zekr_category_id',
        'date',
    ];
protected $casts = [
    'date' => 'datetime',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zekrCategory()
    {
        return $this->belongsTo(ZekrCategory::class);
    }
}
