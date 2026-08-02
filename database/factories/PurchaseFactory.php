<?php

namespace Database\Factories;

use App\Models\Distributor;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
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
            'bill_no' => fake()->unique()->bothify('BL-####'),
            'bill_date' => fake()->dateTimeBetween('-6 months', 'today'),
            'total_amount' => 0,
            'notes' => null,
        ];
    }
}
