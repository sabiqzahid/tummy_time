<?php

namespace Database\Factories;

use App\Models\NewOrder;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewOrderFactory extends Factory
{
    protected $model = NewOrder::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
        ];
    }
}