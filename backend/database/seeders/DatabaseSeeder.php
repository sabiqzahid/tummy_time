<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Food;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\NewOrder;
use App\Models\Feedback;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(7)->create();
        User::factory()->staff()->create();
        User::factory()->superAdmin()->create();
        User::factory()->inactive()->create();
        $allUsers = User::all();

        Category::factory(5)->create();
        $foods = Food::factory(20)->create();

        $allUsers->random(5)->each(function ($user) use ($foods) {
            $user->cart->cartItems()->createMany(
                CartItem::factory(rand(1, 5))->make([
                    'food_id' => $foods->random()->id,
                ])->toArray()
            );
        });

        Order::factory(15)->create()->each(function (Order $order) use ($foods) {
            $orderItemsCount = rand(1, 4);
            $orderItems = $foods->random($orderItemsCount);
            $totalAmount = 0;

            foreach ($orderItems as $food) {
                $quantity = rand(1, 3);
                $order->orderItems()->create([
                    'food_id' => $food->id,
                    'quantity' => $quantity,
                ]);
                $totalAmount += $food->price * $quantity;
            }

            $order->total_amount = $totalAmount;
            $order->save();

            if ($order->payment_status === 'paid' && $order->order_status === 'completed') {
                $order->payment()->create([
                    'payment_type' => rand(0, 1) ? 'cash' : 'card',
                    'payment_date' => $order->order_date,
                ]);
            }

            if ($order->order_status === 'pending') {
                NewOrder::factory()->create(['order_id' => $order->id]);
            }
        });

        $allUsers->random(7)->each(function ($user) use ($foods) {
            Feedback::factory(rand(1, 3))->create([
                'user_id' => $user->id,
                'food_id' => $foods->random()->id,
            ]);
        });
    }
}
