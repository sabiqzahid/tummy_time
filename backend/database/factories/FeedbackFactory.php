<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\User;
use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'food_id' => Food::factory(),
            'comment' => $this->faker->paragraph,
            'feedback_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}