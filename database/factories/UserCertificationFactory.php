<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCertification>
 */
class UserCertificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(['AWS Certified Solutions Architect', 'Google Professional Cloud Architect', 'Certified Scrum Master', 'Cisco Certified Network Associate']),
            'issuer' => $this->faker->randomElement(['Amazon Web Services', 'Google Cloud', 'Scrum Alliance', 'Cisco']),
            'obtained_date' => $this->faker->date('Y-m-d', 'now'),
        ];
    }
}
