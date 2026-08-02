<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 50);
        $rate = fake()->randomFloat(2, 5, 500);

        return [
            'purchase_id' => Purchase::factory(),
            'medicine_id' => Medicine::factory(),
            'medicine_name' => fake()->words(3, true),
            'quantity' => $quantity,
            'rate' => $rate,
            'amount' => round($quantity * $rate, 2),
        ];
    }
}
