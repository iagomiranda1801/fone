<?php

namespace Tests\Unit;

use App\Exceptions\EstoqueInsuficienteException;
use App\Models\Produto;
use App\Services\EstoqueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstoqueServiceTest extends TestCase
{
    use RefreshDatabase;

    private EstoqueService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EstoqueService();
    }

    public function test_registrar_entrada_aumenta_estoque(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        $this->service->registrarEntrada($produto, 5, 50.00);

        $this->assertEquals(15, $produto->fresh()->estoque);
    }

    public function test_registrar_entrada_calcula_custo_medio_ponderado(): void
    {
        // 10 unidades a R$50 + 10 unidades a R$70 = custo médio R$60
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        $this->service->registrarEntrada($produto, 10, 70.00);

        $this->assertEquals(60.00, (float) $produto->fresh()->custo_medio);
    }

    public function test_registrar_entrada_com_estoque_zero_usa_preco_de_entrada(): void
    {
        $produto = Produto::factory()->create(['estoque' => 0, 'custo_medio' => 0.00]);

        $this->service->registrarEntrada($produto, 5, 120.00);

        $this->assertEquals(120.00, (float) $produto->fresh()->custo_medio);
        $this->assertEquals(5, $produto->fresh()->estoque);
    }

    public function test_registrar_saida_diminui_estoque(): void
    {
        $produto = Produto::factory()->create(['estoque' => 20]);

        $this->service->registrarSaida($produto, 8);

        $this->assertEquals(12, $produto->fresh()->estoque);
    }

    public function test_registrar_saida_lanca_exception_quando_estoque_insuficiente(): void
    {
        $produto = Produto::factory()->create(['estoque' => 3, 'nome' => 'Produto Teste']);

        $this->expectException(EstoqueInsuficienteException::class);

        $this->service->registrarSaida($produto, 5);
    }

    public function test_reverter_saida_restaura_estoque(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10]);

        $this->service->reverterSaida($produto, 4);

        $this->assertEquals(14, $produto->fresh()->estoque);
    }
}
