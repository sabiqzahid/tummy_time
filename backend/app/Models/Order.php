<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const CREATED_AT = null;
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'order_date',
        'total_amount',
        'order_status',
        'payment_status',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newOrder()
    {
        return $this->hasOne(NewOrder::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
