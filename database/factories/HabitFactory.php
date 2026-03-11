<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habit>
 */
class HabitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(3),
            'repeat_type' => 1,
            'repeat_interval' => 1,
            'habit_time' => '08:00:00',
            'start_date' => now()->toDateString(),
        ];
    }

    public function morningRun(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Morning Run',
            'repeat_type' => 1,
            'repeat_interval' => 1,
            'habit_time' => '07:00:00',
            'start_date' => now()->subDays(10)->toDateString(),
        ]);
    }

    public function readBook(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Read a Book',
            'repeat_type' => 1,
            'repeat_interval' => 2,
            'habit_time' => '21:00:00',
            'start_date' => now()->subDays(10)->toDateString(),
        ]);
    }

    public function yogaSession(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Yoga Session',
            'repeat_type' => 2,
            'days_of_week' => ['mon', 'wed', 'fri'],
            'habit_time' => '18:00:00',
            'start_date' => now()->subDays(14)->toDateString(),
        ]);
    }

    public function waterPlants(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Water Plants',
            'repeat_type' => 2,
            'days_of_week' => ['sun'],
            'habit_time' => '09:00:00',
            'start_date' => now()->subDays(14)->toDateString(),
        ]);
    }

    public function payMonthlyBills(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Pay Monthly Bills',
            'repeat_type' => 3,
            'day_of_month' => 1,
            'habit_time' => '10:00:00',
            'start_date' => now()->startOfMonth()->subMonth()->toDateString(),
        ]);
    }
}
