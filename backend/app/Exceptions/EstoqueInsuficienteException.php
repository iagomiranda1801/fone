<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class EstoqueInsuficienteException extends Exception
{
    public function __construct(string $produtoNome, int $estoqueAtual, int $quantidadeSolicitada)
    {
        $message = "Estoque insuficiente para o produto '{$produtoNome}'. "
            . "Disponível: {$estoqueAtual}, Solicitado: {$quantidadeSolicitada}.";

        parent::__construct($message, 422);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }
}
