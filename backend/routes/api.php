<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::prefix('relatorios')->group(function () {
        Route::get('vendas', [RelatorioController::class, 'vendas']);
        Route::get('compras', [RelatorioController::class, 'compras']);
        Route::get('estoque', [RelatorioController::class, 'estoque']);
        Route::get('lucratividade', [RelatorioController::class, 'lucratividade']);
        Route::get('produtos-lista', [RelatorioController::class, 'produtos']);
    });

    Route::apiResource('produtos', ProdutoController::class)->only(['index', 'store']);
    Route::patch('produtos/{produto}/nome', [ProdutoController::class, 'updateName']);
    Route::patch('produtos/{produto}/toggle-ativo', [ProdutoController::class, 'toggleAtivo']);
    Route::apiResource('compras', CompraController::class)->only(['index', 'store']);
    Route::apiResource('vendas', VendaController::class)->only(['index', 'store']);
    Route::patch('vendas/{venda}/cancelar', [VendaController::class, 'cancel']);
});
