<?php

namespace App\Services;

use App\Exceptions\EstoqueInsuficienteException;
use App\Models\Produto;
use App\Services\Contracts\EstoqueServiceInterface;

class EstoqueService implements EstoqueServiceInterface
{
    public function registrarEntrada(Produto $produto, int $quantidade, float $precoUnitario): void
    {
        $produto = Produto::lockForUpdate()->find($produto->id);

        $custoTotal = ($produto->custo_medio * $produto->estoque) + ($precoUnitario * $quantidade);
        $novoEstoque = $produto->estoque + $quantidade;

        $produto->update([
            'custo_medio' => $novoEstoque > 0 ? round($custoTotal / $novoEstoque, 2) : 0,
            'estoque' => $novoEstoque,
        ]);
    }

    public function registrarSaida(Produto $produto, int $quantidade): void
    {
        $produto = Produto::lockForUpdate()->find($produto->id);

        if ($produto->estoque < $quantidade) {
            throw new EstoqueInsuficienteException($produto->nome, $produto->estoque, $quantidade);
        }

        $produto->update([
            'estoque' => $produto->estoque - $quantidade,
        ]);
    }

    public function reverterSaida(Produto $produto, int $quantidade): void
    {
        $produto->update([
            'estoque' => $produto->estoque + $quantidade,
        ]);
    }
}
