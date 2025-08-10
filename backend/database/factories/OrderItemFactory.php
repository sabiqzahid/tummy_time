<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'food_id' => Food::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
        ];
    }
}