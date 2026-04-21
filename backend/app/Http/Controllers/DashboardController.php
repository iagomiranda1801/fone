<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalProdutos = Produto::count();
        $produtosAtivos = Produto::where('ativo', true)->count();
        $produtosInativos = $totalProdutos - $produtosAtivos;
        $produtosSemEstoque = Produto::where('ativo', true)->where('estoque', 0)->count();
        $produtosEstoqueBaixo = Produto::where('ativo', true)->where('estoque', '>', 0)->where('estoque', '<=', 5)->count();

        $valorEstoque = Produto::where('ativo', true)
            ->selectRaw('SUM(estoque * custo_medio) as total')
            ->value('total') ?? 0;

        $totalCompras = Compra::count();
        $valorTotalCompras = CompraItem::selectRaw('SUM(quantidade * preco_unitario) as total')->value('total') ?? 0;

        $totalVendas = Venda::count();
        $vendasAtivas = Venda::ativa()->count();
        $vendasCanceladas = $totalVendas - $vendasAtivas;
        $faturamento = Venda::ativa()->sum('total');
        $lucroTotal = Venda::ativa()->sum('lucro');
        $ticketMedio = $vendasAtivas > 0 ? $faturamento / $vendasAtivas : 0;

        // Últimos 7 dias - vendas por dia
        $vendasPorDia = Venda::ativa()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as data, COUNT(*) as total, SUM(total) as faturamento, SUM(lucro) as lucro')
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // Últimos 7 dias - compras por dia
        $comprasPorDia = Compra::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as data, COUNT(*) as total')
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // Preencher dias faltantes
        $dias = collect();
        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i)->format('Y-m-d');
            $vendaDia = $vendasPorDia->firstWhere('data', $data);
            $compraDia = $comprasPorDia->firstWhere('data', $data);
            $dias->push([
                'data' => $data,
                'vendas' => $vendaDia ? (int) $vendaDia->total : 0,
                'faturamento' => $vendaDia ? (float) $vendaDia->faturamento : 0,
                'lucro' => $vendaDia ? (float) $vendaDia->lucro : 0,
                'compras' => $compraDia ? (int) $compraDia->total : 0,
            ]);
        }

        // Top 5 produtos mais vendidos (por quantidade)
        $topProdutosVendidos = VendaItem::join('produtos', 'venda_items.produto_id', '=', 'produtos.id')
            ->join('vendas', 'venda_items.venda_id', '=', 'vendas.id')
            ->where('vendas.cancelada', false)
            ->selectRaw('produtos.id, produtos.nome, SUM(venda_items.quantidade) as total_vendido, SUM(venda_items.quantidade * venda_items.preco_unitario) as receita')
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // Produtos com estoque crítico (ativos, <= 5)
        $estoqueCritico = Produto::where('ativo', true)
            ->where('estoque', '<=', 5)
            ->orderBy('estoque')
            ->limit(8)
            ->get(['id', 'nome', 'estoque', 'custo_medio', 'preco_venda']);

        // Últimas 5 vendas
        $ultimasVendas = Venda::with('itens.produto')
            ->latest()
            ->limit(5)
            ->get(['id', 'cliente', 'total', 'lucro', 'cancelada', 'created_at']);

        // Últimas 5 compras
        $ultimasCompras = Compra::with('itens.produto')
            ->latest()
            ->limit(5)
            ->get(['id', 'fornecedor', 'created_at']);

        // Margem de lucro média
        $margemLucro = $faturamento > 0 ? ($lucroTotal / $faturamento) * 100 : 0;

        return response()->json([
            'resumo' => [
                'total_produtos' => $totalProdutos,
                'produtos_ativos' => $produtosAtivos,
                'produtos_inativos' => $produtosInativos,
                'produtos_sem_estoque' => $produtosSemEstoque,
                'produtos_estoque_baixo' => $produtosEstoqueBaixo,
                'valor_estoque' => (float) $valorEstoque,
                'total_compras' => $totalCompras,
                'valor_total_compras' => (float) $valorTotalCompras,
                'total_vendas' => $totalVendas,
                'vendas_ativas' => $vendasAtivas,
                'vendas_canceladas' => $vendasCanceladas,
                'faturamento' => (float) $faturamento,
                'lucro_total' => (float) $lucroTotal,
                'ticket_medio' => (float) $ticketMedio,
                'margem_lucro' => round((float) $margemLucro, 1),
            ],
            'grafico_7dias' => $dias,
            'top_produtos' => $topProdutosVendidos,
            'estoque_critico' => $estoqueCritico,
            'ultimas_vendas' => $ultimasVendas->map(fn ($v) => [
                'id' => $v->id,
                'cliente' => $v->cliente,
                'total' => (float) $v->total,
                'lucro' => (float) $v->lucro,
                'cancelada' => $v->cancelada,
                'itens_count' => $v->itens->count(),
                'created_at' => $v->created_at,
            ]),
            'ultimas_compras' => $ultimasCompras->map(fn ($c) => [
                'id' => $c->id,
                'fornecedor' => $c->fornecedor,
                'total' => $c->itens->sum(fn ($i) => $i->quantidade * $i->preco_unitario),
                'itens_count' => $c->itens->count(),
                'created_at' => $c->created_at,
            ]),
        ]);
    }
}
