<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendaSeeder extends Seeder
{
    public function run(): void
    {
        $ids = DB::table('produtos')->pluck('id', 'nome');

        $notebook   = $ids['Notebook Dell Inspiron 15'];
        $mouse      = $ids['Mouse Logitech MX Master 3'];
        $teclado    = $ids['Teclado Mecânico Redragon K552'];
        $monitor    = $ids['Monitor LG 24" Full HD IPS'];
        $headset    = $ids['Headset Sony WH-CH510'];
        $webcam     = $ids['Webcam Logitech C920 Full HD'];
        $ssd        = $ids['SSD Kingston A400 480GB'];
        $ram        = $ids['Memória RAM Crucial 8GB DDR4'];
        $hdmi       = $ids['Cabo HDMI 2m 4K Gold'];
        $hub        = $ids['Hub USB 3.0 de 4 Portas'];
        $suporte    = $ids['Suporte para Notebook Articulado'];
        $mousepad   = $ids['Mousepad Gamer XL 90x40cm'];

        /**
         * Cada entrada: [dias_atras, cliente, cancelada, [[produto_id, qtde, preco_unitario], ...]]
         * Vendas canceladas NÃO deduzem estoque.
         */
        $vendas = [
            // Semana 1 (23-18 dias atrás)
            [23, 'Carlos Henrique Lima',     false, [[$notebook, 1, 3999.90], [$mouse, 1, 299.90]]],
            [22, 'Mariana Souza Ferreira',   false, [[$monitor, 1, 999.90], [$hdmi, 2, 34.90]]],
            [21, 'Pedro Alves Costa',        false, [[$teclado, 1, 199.90], [$mousepad, 1, 69.90]]],
            [20, 'Ana Paula Rodrigues',      false, [[$ssd, 1, 299.90], [$ram, 1, 189.90]]],
            [19, 'João Victor Mendes',       false, [[$headset, 1, 249.90], [$hub, 1, 49.90]]],
            [18, 'Larissa Teixeira Nunes',   true,  [[$webcam, 1, 399.90]]],   // cancelada

            // Semana 2 (15-11 dias atrás)
            [15, 'Roberto Carlos Dias',      false, [[$notebook, 1, 3999.90]]],
            [14, 'Fernanda Oliveira Silva',  false, [[$mouse, 2, 299.90], [$teclado, 1, 199.90]]],
            [13, 'Bruno Martins Souza',      false, [[$monitor, 1, 999.90], [$suporte, 1, 89.90]]],
            [12, 'Camila Pereira Santos',    false, [[$ssd, 2, 299.90], [$hdmi, 1, 34.90]]],
            [11, 'Diego Ferreira Lima',      false, [[$headset, 1, 249.90], [$mousepad, 2, 69.90]]],

            // Semana 3 (8-5 dias atrás)
            [8, 'Juliana Costa Almeida',     false, [[$notebook, 1, 3999.90], [$webcam, 1, 399.90]]],
            [7, 'Thiago Barbosa Rocha',      false, [[$ram, 2, 189.90], [$hub, 2, 49.90]]],
            [6, 'Natalia Gomes Faria',       true,  [[$monitor, 1, 999.90], [$hdmi, 1, 34.90]]],  // cancelada
            [5, 'Rafael Santana Vieira',     false, [[$teclado, 1, 199.90], [$mousepad, 1, 69.90], [$mouse, 1, 299.90]]],
            [5, 'Aline Carvalho Melo',       false, [[$ssd, 1, 299.90]]],

            // Últimos 4 dias
            [3, 'Gustavo Lopes Freitas',     false, [[$notebook, 1, 3999.90], [$suporte, 1, 89.90]]],
            [2, 'Isabela Moreira Cunha',     false, [[$headset, 1, 249.90], [$webcam, 1, 399.90]]],
            [1, 'Marcelo Ribeiro Neto',      true,  [[$ram, 1, 189.90], [$ssd, 1, 299.90]]],       // cancelada
            [1, 'Tatiane Araujo Pinto',      false, [[$mouse, 1, 299.90], [$teclado, 1, 199.90], [$hub, 1, 49.90]]],
        ];

        foreach ($vendas as [$diasAtras, $cliente, $cancelada, $itens]) {
            $dataVenda = now()->subDays($diasAtras)->addHours(rand(8, 18));

            $total = 0;
            $lucro = 0;
            $itemsComCusto = [];

            foreach ($itens as [$produtoId, $quantidade, $precoUnitario]) {
                $produto = DB::table('produtos')->where('id', $produtoId)->first();
                $custoUnitario = (float) $produto->custo_medio;

                $total += $precoUnitario * $quantidade;
                $lucro += ($precoUnitario - $custoUnitario) * $quantidade;

                $itemsComCusto[] = [
                    'produto_id'     => $produtoId,
                    'quantidade'     => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'custo_unitario' => $custoUnitario,
                ];
            }

            $vendaId = DB::table('vendas')->insertGetId([
                'cliente'    => $cliente,
                'total'      => round($total, 2),
                'lucro'      => round($lucro, 2),
                'cancelada'  => $cancelada,
                'created_at' => $dataVenda,
                'updated_at' => $dataVenda,
            ]);

            foreach ($itemsComCusto as $item) {
                DB::table('venda_items')->insert(array_merge($item, [
                    'venda_id'   => $vendaId,
                    'created_at' => $dataVenda,
                    'updated_at' => $dataVenda,
                ]));

                // Apenas vendas ativas deduzem estoque
                if (! $cancelada) {
                    DB::table('produtos')
                        ->where('id', $item['produto_id'])
                        ->decrement('estoque', $item['quantidade']);
                }
            }
        }
    }
}
