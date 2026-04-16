<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Models\Produto;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProdutoController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProdutoResource::collection(Produto::all());
    }

    public function store(StoreProdutoRequest $request): ProdutoResource
    {
        $produto = Produto::create($request->validated());

        return new ProdutoResource($produto);
    }
}
