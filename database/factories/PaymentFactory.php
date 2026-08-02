<?php

namespace Database\Factories;

use App\Models\Distributor;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'distributor_id' => Distributor::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'payment_date' => fake()->dateTimeBetween('-6 months', 'today'),
            'reference' => fake()->optional()->numerify('CHQ-####'),
            'notes' => null,
        ];
    }
}
