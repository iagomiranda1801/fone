<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Produto;
use App\Services\Contracts\CompraServiceInterface;
use App\Services\Contracts\EstoqueServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompraService implements CompraServiceInterface
{
    public function __construct(
        private readonly EstoqueServiceInterface $estoqueService,
    ) {}

    public function registrar(array $dados): Compra
    {
        return DB::transaction(function () use ($dados) {
            $compra = Compra::create([
                'fornecedor' => $dados['fornecedor'],
            ]);

            foreach ($dados['produtos'] as $item) {
                $compra->itens()->create([
                    'produto_id' => $item['id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                ]);

                $produto = Produto::findOrFail($item['id']);
                $this->estoqueService->registrarEntrada($produto, $item['quantidade'], $item['preco_unitario']);
            }

            Cache::forget('dashboard_data');

            return $compra->load('itens.produto');
        });
    }
}
