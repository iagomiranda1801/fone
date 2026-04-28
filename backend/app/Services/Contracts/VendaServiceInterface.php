<?php

namespace App\Services\Contracts;

use App\Models\Venda;

interface VendaServiceInterface
{
    public function registrar(array $dados): Venda;

    public function cancelar(Venda $venda): Venda;

    // public function show(Venda $venda): Venda;
}
