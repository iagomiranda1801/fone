<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\Venda;
use App\Services\Contracts\EstoqueServiceInterface;
use App\Services\Contracts\VendaServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VendaService implements VendaServiceInterface
{
    public function __construct(
        private readonly EstoqueServiceInterface $estoqueService,
    ) {}

    public function registrar(array $dados): Venda
    {
        return DB::transaction(function () use ($dados) {
            $venda = Venda::create([
                'cliente' => $dados['cliente'],
            ]);

            $total = 0;
            $lucro = 0;

            foreach ($dados['produtos'] as $item) {
                $produto = Produto::lockForUpdate()->findOrFail($item['id']);

                $this->estoqueService->registrarSaida($produto, $item['quantidade']);

                $venda->itens()->create([
                    'produto_id' => $item['id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'custo_unitario' => $produto->custo_medio,
                ]);

                $total += $item['preco_unitario'] * $item['quantidade'];
                $lucro += ($item['preco_unitario'] - $produto->custo_medio) * $item['quantidade'];
            }

            $venda->update([
                'total' => round($total, 2),
                'lucro' => round($lucro, 2),
            ]);

            Cache::forget('dashboard_data');

            return $venda->load('itens.produto');
        });
    }

    public function cancelar(Venda $venda): Venda
    {
        return DB::transaction(function () use ($venda) {
            if ($venda->cancelada) {
                return $venda;
            }

            foreach ($venda->itens as $item) {
                $this->estoqueService->reverterSaida($item->produto, $item->quantidade);
            }

            $venda->update(['cancelada' => true]);

            Cache::forget('dashboard_data');

            return $venda->fresh('itens.produto');
        });
    } 

    public function show(Venda $venda): Venda
    {
        return $venda->load('itens.produto');
    }
}
