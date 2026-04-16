<?php

namespace App\Services\Contracts;

use App\Models\Produto;

interface EstoqueServiceInterface
{
    public function registrarEntrada(Produto $produto, int $quantidade, float $precoUnitario): void;

    public function registrarSaida(Produto $produto, int $quantidade): void;

    public function reverterSaida(Produto $produto, int $quantidade): void;
}
