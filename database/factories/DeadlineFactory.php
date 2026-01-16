<?php

namespace Database\Factories;

use App\Models\TaxModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deadline>
 */
class DeadlineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tax_model_id' => TaxModel::factory(),
            'deadline_date' => fake()->dateTimeBetween('2026-01-01', '2026-12-31'),
            'deadline_time' => fake()->optional()->time('H:i'),
            'year' => 2026,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
