<?php

namespace Tests\Unit;

use App\Exceptions\EstoqueInsuficienteException;
use App\Models\Produto;
use App\Models\Venda;
use App\Services\EstoqueService;
use App\Services\VendaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendaServiceTest extends TestCase
{
    use RefreshDatabase;

    private VendaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VendaService(new EstoqueService());
    }

    public function test_registrar_cria_venda_com_total_e_lucro_corretos(): void
    {
        // custo: 50, venda: 100 → lucro por unidade: 50, quantidade: 2 → lucro: 100, total: 200
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00, 'preco_venda' => 100.00]);

        $dados = [
            'cliente'  => 'Cliente Teste',
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 100.00],
            ],
        ];

        $venda = $this->service->registrar($dados);

        $this->assertInstanceOf(Venda::class, $venda);
        $this->assertEquals(200.00, (float) $venda->total);
        $this->assertEquals(100.00, (float) $venda->lucro);
        $this->assertFalse((bool) $venda->cancelada);
    }

    public function test_registrar_diminui_estoque_do_produto(): void
    {
        $produto = Produto::factory()->create(['estoque' => 15, 'custo_medio' => 50.00]);

        $dados = [
            'cliente'  => 'Cliente Teste',
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 4, 'preco_unitario' => 80.00],
            ],
        ];

        $this->service->registrar($dados);

        $this->assertEquals(11, $produto->fresh()->estoque);
    }

    public function test_registrar_lanca_exception_quando_estoque_insuficiente(): void
    {
        $produto = Produto::factory()->create(['estoque' => 2]);

        $dados = [
            'cliente'  => 'Cliente Teste',
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 5, 'preco_unitario' => 80.00],
            ],
        ];

        $this->expectException(EstoqueInsuficienteException::class);

        $this->service->registrar($dados);
    }

    public function test_registrar_salva_custo_unitario_no_item(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 75.50]);

        $dados = [
            'cliente'  => 'Cliente Teste',
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1, 'preco_unitario' => 120.00],
            ],
        ];

        $venda = $this->service->registrar($dados);

        $this->assertEquals(75.50, (float) $venda->itens->first()->custo_unitario);
    }

    public function test_cancelar_marca_venda_como_cancelada(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);
        $venda = $this->service->registrar([
            'cliente'  => 'Cliente Teste',
            'produtos' => [['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 100.00]],
        ]);

        $this->service->cancelar($venda);

        $this->assertTrue((bool) $venda->fresh()->cancelada);
    }

    public function test_cancelar_restaura_estoque_dos_produtos(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);
        $venda = $this->service->registrar([
            'cliente'  => 'Cliente Teste',
            'produtos' => [['id' => $produto->id, 'quantidade' => 3, 'preco_unitario' => 100.00]],
        ]);

        // Após a venda o estoque deve ter caído para 7
        $this->assertEquals(7, $produto->fresh()->estoque);

        $this->service->cancelar($venda);

        // Após cancelamento, estoque volta para 10
        $this->assertEquals(10, $produto->fresh()->estoque);
    }

    public function test_cancelar_venda_ja_cancelada_nao_altera_estado(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);
        $venda = $this->service->registrar([
            'cliente'  => 'Cliente Teste',
            'produtos' => [['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 100.00]],
        ]);

        $this->service->cancelar($venda);
        $estoqueAposPrimeiroCancelamento = $produto->fresh()->estoque;

        // Cancelar novamente não deve duplicar a reversão
        $this->service->cancelar($venda->fresh());

        $this->assertEquals($estoqueAposPrimeiroCancelamento, $produto->fresh()->estoque);
    }
}
