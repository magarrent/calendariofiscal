<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationLog>
 */
class NotificationLogFactory extends Factory
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
            'tax_model_id' => \App\Models\TaxModel::factory(),
            'tax_model_reminder_id' => \App\Models\TaxModelReminder::factory(),
            'notification_type' => 'email',
            'sent_at' => now(),
            'details' => [
                'deadline_date' => fake()->date(),
                'days_before' => fake()->randomElement([1, 7, 15, 30]),
            ],
        ];
    }
}
