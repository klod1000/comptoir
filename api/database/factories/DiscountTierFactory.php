<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DiscountTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiscountTier>
 */
final class DiscountTierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => ucfirst(fake()->word()),
            'min_subtotal_cents' => fake()->unique()->numberBetween(1, 1000) * 10000,
            'rate_percent' => '5.00',
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
