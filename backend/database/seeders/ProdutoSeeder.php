<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            [
                'nome'        => 'Notebook Dell Inspiron 15',
                'custo_medio' => 2800.00,
                'preco_venda' => 3999.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Mouse Logitech MX Master 3',
                'custo_medio' => 178.00,
                'preco_venda' => 299.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Teclado Mecânico Redragon K552',
                'custo_medio' => 118.00,
                'preco_venda' => 199.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Monitor LG 24" Full HD IPS',
                'custo_medio' => 648.00,
                'preco_venda' => 999.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Headset Sony WH-CH510',
                'custo_medio' => 148.00,
                'preco_venda' => 249.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Webcam Logitech C920 Full HD',
                'custo_medio' => 248.00,
                'preco_venda' => 399.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'SSD Kingston A400 480GB',
                'custo_medio' => 178.00,
                'preco_venda' => 299.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Memória RAM Crucial 8GB DDR4',
                'custo_medio' => 118.00,
                'preco_venda' => 189.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Cabo HDMI 2m 4K Gold',
                'custo_medio' => 14.50,
                'preco_venda' => 34.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Hub USB 3.0 de 4 Portas',
                'custo_medio' => 24.50,
                'preco_venda' => 49.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Suporte para Notebook Articulado',
                'custo_medio' => 44.00,
                'preco_venda' => 89.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
            [
                'nome'        => 'Mousepad Gamer XL 90x40cm',
                'custo_medio' => 34.00,
                'preco_venda' => 69.90,
                'estoque'     => 0,
                'ativo'       => true,
            ],
        ];

        foreach ($produtos as $produto) {
            DB::table('produtos')->insertOrIgnore(array_merge($produto, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
