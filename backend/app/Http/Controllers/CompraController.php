<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Http\Resources\CompraResource;
use App\Models\Compra;
use App\Services\Contracts\CompraServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompraController extends Controller
{
    public function __construct(
        private readonly CompraServiceInterface $compraService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $compras = Compra::with('itens.produto')->latest()->get();

        return CompraResource::collection($compras);
    }

    public function store(StoreCompraRequest $request): CompraResource
    {
        $compra = $this->compraService->registrar($request->validated());

        return new CompraResource($compra);
    }
}
