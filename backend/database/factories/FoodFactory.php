<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodFactory extends Factory
{
    protected $model = Food::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(2),
            'price' => $this->faker->randomFloat(2, 5, 50),
            'is_available' => $this->faker->boolean(90),
            'stock' => $this->faker->numberBetween(0, 100),
            'sold' => $this->faker->numberBetween(0, 200),
            'image_url' => $this->faker->imageUrl(),
            'category_id' => Category::factory(),
        ];
    }
}