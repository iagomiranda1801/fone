<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::apiResource('produtos', ProdutoController::class)->only(['index', 'store']);
    Route::patch('produtos/{produto}/nome', [ProdutoController::class, 'updateName']);
    Route::patch('produtos/{produto}/toggle-ativo', [ProdutoController::class, 'toggleAtivo']);
    Route::apiResource('compras', CompraController::class)->only(['index', 'store']);
    Route::apiResource('vendas', VendaController::class)->only(['index', 'store']);
    Route::patch('vendas/{venda}/cancelar', [VendaController::class, 'cancel']);
});
