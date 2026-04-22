<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProdutoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProdutoResource::collection(Produto::orderBy('nome')->get());
    }

    public function store(StoreProdutoRequest $request): ProdutoResource
    {
        $produto = Produto::create($request->validated());

        return new ProdutoResource($produto);
    }

    public function updateName(Request $request, Produto $produto): ProdutoResource
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'min:3'],
        ], [
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',
        ]);

        $produto->update($validated);

        return new ProdutoResource($produto);
    }

    public function toggleAtivo(Produto $produto): ProdutoResource
    {
        $produto->update(['ativo' => !$produto->ativo]);

        return new ProdutoResource($produto);
    }
}
