<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductBatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            // PERBAIKAN: Gunakan 'batch_number' (bukan batch_no)
            'batch_number' => 'BATCH-' . strtoupper($this->faker->bothify('?####')),

            'stock' => $this->faker->numberBetween(10, 100),

            'expired_date' => $this->faker->dateTimeBetween('now', '+2 years'),

            'purchase_price' => $this->faker->numberBetween(5000, 50000),
        ];
    }
}
