<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSkill>
 */
class UserSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skill_name' => $this->faker->randomElement(['PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React', 'Docker', 'AWS', 'Python', 'SQL']),
        ];
    }
}
