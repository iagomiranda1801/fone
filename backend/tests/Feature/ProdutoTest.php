<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdutoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_pode_listar_produtos(): void
    {
        Produto::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->getJson('/api/produtos');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_listar_produtos_requer_autenticacao(): void
    {
        $this->getJson('/api/produtos')->assertUnauthorized();
    }

    public function test_pode_criar_produto(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/produtos', [
            'nome'        => 'Produto Teste ABC',
            'preco_venda' => 199.90,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.nome', 'Produto Teste ABC')
            ->assertJsonPath('data.preco_venda', 199.9);

        $this->assertDatabaseHas('produtos', ['nome' => 'Produto Teste ABC']);
    }

    public function test_criar_produto_requer_nome_e_preco_venda(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/produtos', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nome', 'preco_venda']);
    }

    public function test_criar_produto_preco_venda_deve_ser_positivo(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/produtos', [
            'nome'        => 'Produto Teste',
            'preco_venda' => -10,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['preco_venda']);
    }

    public function test_pode_alterar_nome_do_produto(): void
    {
        $produto = Produto::factory()->create(['nome' => 'Nome Antigo']);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/produtos/{$produto->id}/nome", ['nome' => 'Nome Novo']);

        $response->assertOk()
            ->assertJsonPath('data.nome', 'Nome Novo');

        $this->assertDatabaseHas('produtos', ['id' => $produto->id, 'nome' => 'Nome Novo']);
    }

    public function test_pode_alternar_status_ativo_do_produto(): void
    {
        $produto = Produto::factory()->create(['ativo' => true]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/produtos/{$produto->id}/toggle-ativo");

        $response->assertOk()
            ->assertJsonPath('data.ativo', false);

        // Alternar de volta para ativo
        $this->actingAs($this->user)
            ->patchJson("/api/produtos/{$produto->id}/toggle-ativo")
            ->assertJsonPath('data.ativo', true);
    }

    public function test_produtos_sao_retornados_em_ordem_alfabetica(): void
    {
        Produto::factory()->create(['nome' => 'Zebra Pro']);
        Produto::factory()->create(['nome' => 'Alfa Premium']);
        Produto::factory()->create(['nome' => 'Mouse Gamer']);

        $response = $this->actingAs($this->user)->getJson('/api/produtos');

        $response->assertOk();
        $nomes = collect($response->json('data'))->pluck('nome')->toArray();

        $this->assertEquals(['Alfa Premium', 'Mouse Gamer', 'Zebra Pro'], $nomes);
    }
}
