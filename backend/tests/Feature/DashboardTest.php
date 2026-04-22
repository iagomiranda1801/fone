<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requer_autenticacao(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }

    public function test_dashboard_retorna_estrutura_correta(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/dashboard');

        $response->assertOk()
            ->assertJsonStructure([
                'resumo' => [
                    'total_produtos',
                    'produtos_ativos',
                    'produtos_inativos',
                    'produtos_sem_estoque',
                    'produtos_estoque_baixo',
                    'valor_estoque',
                    'total_compras',
                    'valor_total_compras',
                    'total_vendas',
                    'vendas_ativas',
                    'vendas_canceladas',
                    'faturamento',
                    'lucro_total',
                    'ticket_medio',
                    'margem_lucro',
                ],
                'grafico_7dias',
                'top_produtos',
                'estoque_critico',
                'ultimas_vendas',
                'ultimas_compras',
            ]);
    }

    public function test_dashboard_resumo_conta_produtos_corretamente(): void
    {
        $user = User::factory()->create();

        Produto::factory()->count(3)->create(['ativo' => true]);
        Produto::factory()->count(2)->inativo()->create();

        $response = $this->actingAs($user)->getJson('/api/dashboard');

        $response->assertOk()
            ->assertJsonPath('resumo.total_produtos', 5)
            ->assertJsonPath('resumo.produtos_ativos', 3)
            ->assertJsonPath('resumo.produtos_inativos', 2);
    }

    public function test_dashboard_grafico_7dias_retorna_sete_entradas(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/dashboard');

        $response->assertOk();
        $this->assertCount(7, $response->json('grafico_7dias'));
    }

    public function test_dashboard_calcula_faturamento_apenas_de_vendas_ativas(): void
    {
        $user    = User::factory()->create();
        $produto = Produto::factory()->create(['estoque' => 10, 'custo_medio' => 50.00]);

        // Venda ativa: total 200
        Venda::factory()->create([
            'total'     => 200.00,
            'lucro'     => 100.00,
            'cancelada' => false,
        ]);

        // Venda cancelada: não deve entrar no faturamento
        Venda::factory()->create([
            'total'     => 500.00,
            'lucro'     => 250.00,
            'cancelada' => true,
        ]);

        $response = $this->actingAs($user)->getJson('/api/dashboard');

        $response->assertOk()
            ->assertJsonPath('resumo.faturamento', 200)
            ->assertJsonPath('resumo.vendas_ativas', 1)
            ->assertJsonPath('resumo.vendas_canceladas', 1);
    }
}
