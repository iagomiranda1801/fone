<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdutoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $compraItens = $this->whenLoaded('compraItens');
        $menorPreco = null;
        $maiorPreco = null;

        if ($compraItens && count($compraItens) > 0) {
            $menorPreco = (float) $compraItens->min('preco_unitario');
            $maiorPreco = (float) $compraItens->max('preco_unitario');
        }

        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'custo_medio' => (float) $this->custo_medio,
            'preco_venda' => (float) $this->preco_venda,
            'estoque' => $this->estoque,
            'ativo' => $this->ativo,
            'menor_preco_compra' => $menorPreco,
            'maior_preco_compra' => $maiorPreco,
            'created_at' => $this->created_at,
        ];
    }
}
