<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserEducation>
 */
class UserEducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_name' => $this->faker->company() . ' University',
            'degree' => $this->faker->randomElement(['Bachelor of Science', 'Bachelor of Arts', 'Master of Science', 'PhD']),
            'field' => $this->faker->randomElement(['Computer Science', 'Data Science', 'Business Administration', 'Marketing', 'Physics']),
            'country' => $this->faker->country(),
            'start_date' => $this->faker->date('Y-m-d', '-4 years'),
            'end_date' => $this->faker->date('Y-m-d', '-1 year'),
        ];
    }
}
