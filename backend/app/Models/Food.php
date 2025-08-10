<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    public const CREATED_AT = null;
    public const UPDATED_AT = null;

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

    protected $casts = [
        'is_available' => 'boolean',
        'stock' => 'integer',
        'sold' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}