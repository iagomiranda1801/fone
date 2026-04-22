<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_pode_listar_vendas(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        $this->actingAs($this->user)->postJson('/api/vendas', [
            'cliente'  => 'Cliente A',
            'produtos' => [['id' => $produto->id, 'quantidade' => 1, 'preco_unitario' => 100.00]],
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/vendas');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_pode_registrar_venda(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        $response = $this->actingAs($this->user)->postJson('/api/vendas', [
            'cliente'  => 'Cliente Teste',
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 100.00],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.cliente', 'Cliente Teste')
            ->assertJsonPath('data.total', 200)
            ->assertJsonPath('data.cancelada', false);
    }

    public function test_registrar_venda_diminui_estoque(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        $this->actingAs($this->user)->postJson('/api/vendas', [
            'cliente'  => 'Cliente Teste',
            'produtos' => [['id' => $produto->id, 'quantidade' => 3, 'preco_unitario' => 100.00]],
        ]);

        $this->assertEquals(7, $produto->fresh()->estoque);
    }

    public function test_registrar_venda_com_estoque_insuficiente_retorna_422(): void
    {
        $produto = Produto::factory()->create(['estoque' => 2, 'custo_medio' => 50.00]);

        $response = $this->actingAs($this->user)->postJson('/api/vendas', [
            'cliente'  => 'Cliente Teste',
            'produtos' => [['id' => $produto->id, 'quantidade' => 10, 'preco_unitario' => 100.00]],
        ]);

        $response->assertUnprocessable();
    }

    public function test_pode_cancelar_venda(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);
        $venda = $this->actingAs($this->user)->postJson('/api/vendas', [
            'cliente'  => 'Cliente Teste',
            'produtos' => [['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 100.00]],
        ])->json('data');

        $response = $this->actingAs($this->user)
            ->patchJson("/api/vendas/{$venda['id']}/cancelar");

        $response->assertOk()
            ->assertJsonPath('data.cancelada', true);
    }

    public function test_cancelar_venda_restaura_estoque(): void
    {
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);
        $venda = $this->actingAs($this->user)->postJson('/api/vendas', [
            'cliente'  => 'Cliente Teste',
            'produtos' => [['id' => $produto->id, 'quantidade' => 4, 'preco_unitario' => 100.00]],
        ])->json('data');

        $this->assertEquals(6, $produto->fresh()->estoque);

        $this->actingAs($this->user)->patchJson("/api/vendas/{$venda['id']}/cancelar");

        $this->assertEquals(10, $produto->fresh()->estoque);
    }

    public function test_registrar_venda_requer_cliente_e_produtos(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/vendas', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cliente', 'produtos']);
    }

    public function test_listar_vendas_requer_autenticacao(): void
    {
        $this->getJson('/api/vendas')->assertUnauthorized();
    }
}
