<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentRequestFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement(['submitted', 'submitted', 'submitted', 'revision', 'approved', 'rejected']);
        $submittedAt = fake()->dateTimeBetween('-6 months', 'now');
        $decidedAt = in_array($status, ['approved', 'rejected'])
            ? fake()->dateTimeBetween($submittedAt, 'now')
            : null;

        return [
            'document_type' => fake()->randomElement(['SIUP', 'IMB', 'TDP', 'NIB', 'Izin Lingkungan']),
            'status' => $status,
            'submitted_at' => $submittedAt,
            'decided_at' => $decidedAt,
            'note' => fake()->optional()->sentence(),
        ];
    }
}