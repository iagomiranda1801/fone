<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venda>
 */
class VendaFactory extends Factory
{
    public function definition(): array
    {
        $total = $this->faker->randomFloat(2, 50, 5000);

        return [
            'cliente'   => $this->faker->name(),
            'total'     => $total,
            'lucro'     => round($total * $this->faker->randomFloat(2, 0.1, 0.4), 2),
            'cancelada' => false,
        ];
    }

    public function cancelada(): static
    {
        return $this->state(['cancelada' => true]);
    }
}
