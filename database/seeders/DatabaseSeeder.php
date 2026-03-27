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
        // 1. Regular Test User
        $user = User::firstOrCreate(
            ['email' => 'test@test.com'],
            ['name' => 'Test User', 'password' => bcrypt('password'), 'role_id' => 0]
        );

        // 2. Admin Test User
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'role_id' => 1]
        );

        // Add 16 tasks for the first user
        Task::factory(16)->create([
            'user_id' => $user->id
        ]);

        // Add 15 journals for the regular user and admin with unique dates
        for ($i = 0; $i < 15; $i++) {
            \App\Models\Journal::factory()->create([
                'user_id' => $user->id,
                'entry_date' => now()->subDays($i)->format('Y-m-d')
            ]);
            \App\Models\Journal::factory()->create([
                'user_id' => $admin->id,
                'entry_date' => now()->subDays($i)->format('Y-m-d')
            ]);
        }

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
                \App\Models\Task::factory(5)
            )
            ->has(
                \App\Models\Journal::factory(15)->sequence(fn ($sq) => ['entry_date' => now()->subDays($sq->index)->format('Y-m-d')])
            )
            ->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(HabitSeeder::class);
    }
}
