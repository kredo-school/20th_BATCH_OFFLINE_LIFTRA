<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserExperience>
 */
class UserExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_title' => $this->faker->jobTitle(),
            'company_name' => $this->faker->company(),
            'employment_type' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract', 'Internship']),
            'start_date' => $this->faker->date('Y-m-d', '-2 years'),
            'end_date' => $this->faker->date('Y-m-d', 'now'),
            'currently_working' => $this->faker->boolean(20),
            'description' => $this->faker->paragraph(),
        ];
    }
}
