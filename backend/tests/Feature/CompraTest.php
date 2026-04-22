<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_pode_listar_compras(): void
    {
        $produto = Produto::factory()->create(['estoque' => 0, 'custo_medio' => 0]);

        $this->actingAs($this->user)->postJson('/api/compras', [
            'fornecedor' => 'Fornecedor A',
            'produtos'   => [['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 50.00]],
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/compras');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_pode_registrar_compra(): void
    {
        $produto = Produto::factory()->create(['estoque' => 0, 'custo_medio' => 0]);

        $response = $this->actingAs($this->user)->postJson('/api/compras', [
            'fornecedor' => 'Fornecedor Teste Ltda',
            'produtos'   => [
                ['id' => $produto->id, 'quantidade' => 10, 'preco_unitario' => 80.00],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.fornecedor', 'Fornecedor Teste Ltda')
            ->assertJsonCount(1, 'data.itens');
    }

    public function test_registrar_compra_aumenta_estoque_do_produto(): void
    {
        $produto = Produto::factory()->create(['estoque' => 5, 'custo_medio' => 50.00]);

        $this->actingAs($this->user)->postJson('/api/compras', [
            'fornecedor' => 'Fornecedor Teste',
            'produtos'   => [
                ['id' => $produto->id, 'quantidade' => 10, 'preco_unitario' => 60.00],
            ],
        ]);

        $this->assertEquals(15, $produto->fresh()->estoque);
    }

    public function test_registrar_compra_requer_fornecedor_e_produtos(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/compras', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['fornecedor', 'produtos']);
    }

    public function test_registrar_compra_requer_pelo_menos_um_produto(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/compras', [
            'fornecedor' => 'Fornecedor Teste',
            'produtos'   => [],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['produtos']);
    }

    public function test_listar_compras_requer_autenticacao(): void
    {
        $this->getJson('/api/compras')->assertUnauthorized();
    }
}
