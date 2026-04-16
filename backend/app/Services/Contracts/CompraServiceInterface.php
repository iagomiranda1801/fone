<?php

namespace App\Services\Contracts;

use App\Models\Compra;

interface CompraServiceInterface
{
    public function registrar(array $dados): Compra;
}
