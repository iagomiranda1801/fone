<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        $custo = $this->faker->randomFloat(2, 20, 500);

        return [
            'nome'        => $this->faker->words(3, true),
            'custo_medio' => $custo,
            'preco_venda' => round($custo * $this->faker->randomFloat(2, 1.2, 2.0), 2),
            'estoque'     => $this->faker->numberBetween(1, 100),
            'ativo'       => true,
        ];
    }

    public function inativo(): static
    {
        return $this->state(['ativo' => false]);
    }

    public function semEstoque(): static
    {
        return $this->state(['estoque' => 0]);
    }

    public function estoqueBaixo(): static
    {
        return $this->state(['estoque' => $this->faker->numberBetween(1, 5)]);
    }
}
