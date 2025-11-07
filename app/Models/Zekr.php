<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zekr extends Model
{
    use HasFactory;

    protected $fillable = [
        'zekr_category_id',
        'content',
        'repetition',
    ];

    /**
     * العلاقة: الذكر يتبع لفئة (نوع ذكر)
     */
    public function category()
    {
        return $this->belongsTo(ZekrCategory::class, 'zekr_category_id');
    }
}
