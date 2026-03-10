<?php

namespace Database\Factories;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'priority_type' => $this->faker->randomElement([
                Task::IMPORTANT_URGENT,
                Task::IMPORTANT_NOT_URGENT,
                Task::NOT_IMPORTANT_URGENT,
                Task::NOT_IMPORTANT_NOT_URGENT,
                ]),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'start_date' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'completed' => $this->faker->boolean(20), //20% completed
        ];
    }
}
