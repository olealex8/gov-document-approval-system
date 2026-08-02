<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'registration_number' => 'AHU-' . fake()->unique()->numerify('#####'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
        ];
    }
}