<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@test.com'],
            ['name' => 'test', 'password' => bcrypt('password')]
        );

        Task::factory(16)->create([
            'user_id' => $user->id
        ]);

        \App\Models\User::factory(2)
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
            ->has(
                \App\Models\Task::factory(5) // ←task追加
            )
            ->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(HabitSeeder::class);
    }
}
