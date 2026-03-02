<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(3)
        ->has(
            \App\Models\Category::factory(2)
                ->has(
                    \App\Models\Goal::factory(2)
                        ->state(function (array $attributes, \App\Models\Category $category) {
                            return [
                                'user_id' => $category->user_id,
                            ];
                        })
                        ->has(
                            \App\Models\Milestone::factory(2)
                        )
                )
        )
        ->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
