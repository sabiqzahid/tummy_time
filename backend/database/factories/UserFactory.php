<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_staff' => false,
            'is_super_admin' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate a specific password for the user.
     *
     * @param string $password
     * @return $this
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => $password,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_staff' => true,
        ]);
    }

    /**
     * Indicate that the user is an Super Admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_staff' => true,
            'is_super_admin' => true,
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Configure the model factory.
     *
     * We will create a cart for each user after it has been created.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $user->cart()->create([]);
        });
    }
}