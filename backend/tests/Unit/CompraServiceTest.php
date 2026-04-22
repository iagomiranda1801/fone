<?php

namespace Tests\Unit;

use App\Models\Compra;
use App\Models\Produto;
use App\Services\CompraService;
use App\Services\EstoqueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraServiceTest extends TestCase
{
    use RefreshDatabase;

    private CompraService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CompraService(new EstoqueService());
    }

    public function test_registrar_cria_compra_com_itens(): void
    {
        $produto1 = Produto::factory()->create(['estoque' => 0, 'custo_medio' => 0]);
        $produto2 = Produto::factory()->create(['estoque' => 0, 'custo_medio' => 0]);

        $dados = [
            'fornecedor' => 'Fornecedor Teste Ltda',
            'produtos' => [
                ['id' => $produto1->id, 'quantidade' => 5, 'preco_unitario' => 100.00],
                ['id' => $produto2->id, 'quantidade' => 3, 'preco_unitario' => 200.00],
            ],
        ];

        $compra = $this->service->registrar($dados);

        $this->assertInstanceOf(Compra::class, $compra);
        $this->assertEquals('Fornecedor Teste Ltda', $compra->fornecedor);
        $this->assertCount(2, $compra->itens);
    }

    public function test_registrar_aumenta_estoque_dos_produtos(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        $dados = [
            'fornecedor' => 'Fornecedor Teste Ltda',
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 5, 'preco_unitario' => 60.00],
            ],
        ];

        $this->service->registrar($dados);

        $this->assertEquals(15, $produto->fresh()->estoque);
    }

    public function test_registrar_calcula_custo_medio_ponderado(): void
    {
        // 10 unidades a R$50 + 5 unidades a R$80 = (500 + 400) / 15 = R$60
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        $dados = [
            'fornecedor' => 'Fornecedor Teste Ltda',
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 5, 'preco_unitario' => 80.00],
            ],
        ];

        $this->service->registrar($dados);

        $this->assertEquals(60.00, (float) $produto->fresh()->custo_medio);
    }
}
