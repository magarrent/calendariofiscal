<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxModel>
 */
class TaxModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['iva', 'irpf', 'retenciones', 'sociedades', 'otros'];
        $frequencies = ['monthly', 'quarterly', 'annual', 'one-time'];
        $applicableTo = [
            ['autonomo'],
            ['autonomo', 'pyme'],
            ['pyme', 'large_corp'],
            ['autonomo', 'pyme', 'large_corp'],
        ];

        return [
            'model_number' => fake()->unique()->numerify('###'),
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'instructions' => fake()->paragraphs(2, true),
            'penalties' => fake()->paragraph(),
            'frequency' => fake()->randomElement($frequencies),
            'applicable_to' => fake()->randomElement($applicableTo),
            'aeat_url' => 'https://sede.agenciatributaria.gob.es',
            'category' => fake()->randomElement($categories),
            'year' => 2026,
        ];
    }
}
