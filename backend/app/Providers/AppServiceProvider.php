<?php

namespace App\Providers;

use App\Services\CompraService;
use App\Services\Contracts\CompraServiceInterface;
use App\Services\Contracts\EstoqueServiceInterface;
use App\Services\Contracts\VendaServiceInterface;
use App\Services\EstoqueService;
use App\Services\VendaService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        EstoqueServiceInterface::class => EstoqueService::class,
        CompraServiceInterface::class => CompraService::class,
        VendaServiceInterface::class => VendaService::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
