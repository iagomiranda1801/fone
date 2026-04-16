<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendaRequest;
use App\Http\Resources\VendaResource;
use App\Models\Venda;
use App\Services\Contracts\VendaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VendaController extends Controller
{
    public function __construct(
        private readonly VendaServiceInterface $vendaService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $vendas = Venda::with('itens.produto')->latest()->get();

        return VendaResource::collection($vendas);
    }

    public function store(StoreVendaRequest $request): VendaResource
    {
        $venda = $this->vendaService->registrar($request->validated());

        return new VendaResource($venda);
    }

    public function cancel(Venda $venda): VendaResource
    {
        $venda = $this->vendaService->cancelar($venda);

        return new VendaResource($venda);
    }
}
