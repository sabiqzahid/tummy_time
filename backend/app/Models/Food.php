<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_available',
        'stock',
        'sold',
        'image_url',
        'category_id',
    ];

    /**
     * Get the category that the food item belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}