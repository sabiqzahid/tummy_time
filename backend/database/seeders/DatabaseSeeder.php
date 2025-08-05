<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Food;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(7)->create();

        User::factory()->staff()->create();

        User::factory()->superAdmin()->create();

        User::factory()->inactive()->create();

        Category::factory(5)->create();

        Food::factory(20)->create();
    }
}
