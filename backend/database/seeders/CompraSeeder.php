<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompraSeeder extends Seeder
{
    public function run(): void
    {
        // IDs são obtidos pelo nome após ProdutoSeeder rodar
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
         * Cada entrada: [dias_atras, fornecedor, [[produto_id, qtde, preco_unitario], ...]]
         * O seeder cria compra + itens e atualiza estoque + custo_medio dos produtos.
         */
        $compras = [
            // Semana 1 (28-22 dias atrás)
            [28, 'Dell Distribuidora Brasil',    [[$notebook, 5,  2800.00], [$monitor, 3,  648.00]]],
            [26, 'Logitech do Brasil Ltda',      [[$mouse, 10,    178.00], [$webcam, 5,   248.00]]],
            [24, 'Redragon Imports',             [[$teclado, 8,   118.00], [$mousepad, 10, 34.00]]],
            [22, 'Kingston Technology BR',       [[$ssd, 8,       178.00], [$ram, 10,    118.00]]],
            [21, 'Sony Electronics BR',          [[$headset, 6,   148.00]]],

            // Semana 2 (18-14 dias atrás)
            [18, 'Distribuidora TechParts',      [[$hdmi, 20,  14.50], [$hub, 15,  24.50], [$suporte, 8, 44.00]]],
            [16, 'Dell Distribuidora Brasil',    [[$notebook, 3,  2850.00]]],
            [14, 'Logitech do Brasil Ltda',      [[$mouse, 5,   180.00], [$webcam, 3,  252.00]]],

            // Semana 3 (11-7 dias atrás)
            [11, 'Kingston Technology BR',       [[$ssd, 5,  182.00], [$ram, 8, 120.00]]],
            [10, 'Redragon Imports',             [[$teclado, 5, 120.00]]],
            [9,  'Sony Electronics BR',          [[$headset, 4, 150.00]]],
            [7,  'Distribuidora TechParts',      [[$hdmi, 15, 14.50], [$hub, 10, 25.00]]],

            // Semana 4 (5-2 dias atrás — reposição)
            [5, 'Dell Distribuidora Brasil',     [[$notebook, 2, 2900.00], [$monitor, 2, 652.00]]],
            [4, 'Logitech do Brasil Ltda',       [[$mouse, 8,  182.00]]],
            [2, 'Distribuidora TechParts',       [[$mousepad, 8, 35.00], [$suporte, 5, 45.00]]],
        ];

        foreach ($compras as [$diasAtras, $fornecedor, $itens]) {
            $dataCompra = now()->subDays($diasAtras);

            $compraId = DB::table('compras')->insertGetId([
                'fornecedor' => $fornecedor,
                'created_at' => $dataCompra,
                'updated_at' => $dataCompra,
            ]);

            foreach ($itens as [$produtoId, $quantidade, $precoUnitario]) {
                DB::table('compra_items')->insert([
                    'compra_id'      => $compraId,
                    'produto_id'     => $produtoId,
                    'quantidade'     => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'created_at'     => $dataCompra,
                    'updated_at'     => $dataCompra,
                ]);

                // Atualiza estoque e custo médio ponderado
                $produto = DB::table('produtos')->where('id', $produtoId)->first();

                $estoqueAtual  = (int) $produto->estoque;
                $custoAtual    = (float) $produto->custo_medio;
                $novoEstoque   = $estoqueAtual + $quantidade;
                $novoCusto     = $novoEstoque > 0
                    ? (($custoAtual * $estoqueAtual) + ($precoUnitario * $quantidade)) / $novoEstoque
                    : $precoUnitario;

                DB::table('produtos')->where('id', $produtoId)->update([
                    'estoque'     => $novoEstoque,
                    'custo_medio' => round($novoCusto, 2),
                    'updated_at'  => $dataCompra,
                ]);
            }
        }
    }
}
