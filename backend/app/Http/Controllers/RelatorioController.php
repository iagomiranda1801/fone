<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function vendas(Request $request): JsonResponse
    {
        $query = Venda::with('itens.produto')->latest();

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        if ($request->filled('cliente')) {
            $query->where('cliente', 'like', '%' . $request->cliente . '%');
        }
        if ($request->filled('status')) {
            $query->where('cancelada', $request->status === 'cancelada');
        }
        if ($request->filled('produto_id')) {
            $query->whereHas('itens', fn($q) => $q->where('produto_id', $request->produto_id));
        }

        $vendas = $query->limit(1000)->get();

        $rows = $vendas->map(fn($v) => [
            'id'          => $v->id,
            'cliente'     => $v->cliente,
            'itens'       => $v->itens->map(fn($i) => [
                'produto'        => $i->produto?->nome ?? '—',
                'quantidade'     => $i->quantidade,
                'preco_unitario' => (float) $i->preco_unitario,
                'custo_unitario' => (float) $i->custo_unitario,
                'subtotal'       => round($i->quantidade * $i->preco_unitario, 2),
            ]),
            'total'       => (float) $v->total,
            'lucro'       => (float) $v->lucro,
            'cancelada'   => $v->cancelada,
            'created_at'  => $v->created_at?->format('d/m/Y H:i'),
        ]);

        $totalizadores = [
            'total_vendas'     => $vendas->count(),
            'vendas_ativas'    => $vendas->where('cancelada', false)->count(),
            'vendas_canceladas'=> $vendas->where('cancelada', true)->count(),
            'faturamento'      => (float) $vendas->where('cancelada', false)->sum('total'),
            'lucro_total'      => (float) $vendas->where('cancelada', false)->sum('lucro'),
        ];

        return response()->json(['data' => $rows, 'totalizadores' => $totalizadores]);
    }

    public function compras(Request $request): JsonResponse
    {
        $query = Compra::with('itens.produto')->latest();

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        if ($request->filled('fornecedor')) {
            $query->where('fornecedor', 'like', '%' . $request->fornecedor . '%');
        }
        if ($request->filled('produto_id')) {
            $query->whereHas('itens', fn($q) => $q->where('produto_id', $request->produto_id));
        }

        $compras = $query->limit(1000)->get();

        $rows = $compras->map(fn($c) => [
            'id'         => $c->id,
            'fornecedor' => $c->fornecedor,
            'itens'      => $c->itens->map(fn($i) => [
                'produto'        => $i->produto?->nome ?? '—',
                'quantidade'     => $i->quantidade,
                'preco_unitario' => (float) $i->preco_unitario,
                'subtotal'       => round($i->quantidade * $i->preco_unitario, 2),
            ]),
            'total'      => round($c->itens->sum(fn($i) => $i->quantidade * $i->preco_unitario), 2),
            'created_at' => $c->created_at?->format('d/m/Y H:i'),
        ]);

        $totalizadores = [
            'total_compras'  => $compras->count(),
            'valor_total'    => (float) $rows->sum('total'),
        ];

        return response()->json(['data' => $rows, 'totalizadores' => $totalizadores]);
    }

    public function estoque(Request $request): JsonResponse
    {
        $query = Produto::query();

        if ($request->filled('status')) {
            $query->where('ativo', $request->status === 'ativo');
        }
        if ($request->filled('estoque_nivel')) {
            if ($request->estoque_nivel === 'zerado') {
                $query->where('estoque', 0);
            } elseif ($request->estoque_nivel === 'baixo') {
                $query->where('estoque', '>', 0)->where('estoque', '<=', 5);
            } elseif ($request->estoque_nivel === 'normal') {
                $query->where('estoque', '>', 5);
            }
        }
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        $produtos = $query->orderBy('nome')->get();

        $rows = $produtos->map(fn($p) => [
            'id'           => $p->id,
            'nome'         => $p->nome,
            'estoque'      => $p->estoque,
            'custo_medio'  => (float) $p->custo_medio,
            'preco_venda'  => (float) $p->preco_venda,
            'margem'       => $p->custo_medio > 0
                ? round((($p->preco_venda - $p->custo_medio) / $p->custo_medio) * 100, 1)
                : 0,
            'valor_estoque'=> round($p->estoque * $p->custo_medio, 2),
            'ativo'        => $p->ativo,
            'nivel'        => match(true) {
                $p->estoque === 0         => 'zerado',
                $p->estoque <= 5          => 'baixo',
                default                   => 'normal',
            },
        ]);

        $totalizadores = [
            'total_produtos'    => $produtos->count(),
            'produtos_ativos'   => $produtos->where('ativo', true)->count(),
            'valor_total_estoque'=> (float) $rows->sum('valor_estoque'),
            'produtos_zerados'  => $rows->where('nivel', 'zerado')->count(),
            'produtos_baixo'    => $rows->where('nivel', 'baixo')->count(),
        ];

        return response()->json(['data' => $rows, 'totalizadores' => $totalizadores]);
    }

    public function lucratividade(Request $request): JsonResponse
    {
        $query = VendaItem::join('produtos', 'venda_items.produto_id', '=', 'produtos.id')
            ->join('vendas', 'venda_items.venda_id', '=', 'vendas.id')
            ->where('vendas.cancelada', false);

        if ($request->filled('data_inicio')) {
            $query->whereDate('vendas.created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('vendas.created_at', '<=', $request->data_fim);
        }
        if ($request->filled('produto_id')) {
            $query->where('venda_items.produto_id', $request->produto_id);
        }

        $rows = $query->selectRaw('
                produtos.id,
                produtos.nome,
                SUM(venda_items.quantidade) as total_vendido,
                SUM(venda_items.quantidade * venda_items.preco_unitario) as receita,
                SUM(venda_items.quantidade * venda_items.custo_unitario) as custo,
                SUM(venda_items.quantidade * (venda_items.preco_unitario - venda_items.custo_unitario)) as lucro
            ')
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderByDesc('lucro')
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'nome'         => $r->nome,
                'total_vendido'=> (int) $r->total_vendido,
                'receita'      => (float) $r->receita,
                'custo'        => (float) $r->custo,
                'lucro'        => (float) $r->lucro,
                'margem'       => $r->receita > 0
                    ? round(($r->lucro / $r->receita) * 100, 1)
                    : 0,
            ]);

        $totalizadores = [
            'receita_total' => (float) $rows->sum('receita'),
            'custo_total'   => (float) $rows->sum('custo'),
            'lucro_total'   => (float) $rows->sum('lucro'),
            'margem_media'  => $rows->avg('margem') ? round($rows->avg('margem'), 1) : 0,
        ];

        return response()->json(['data' => $rows, 'totalizadores' => $totalizadores]);
    }

    public function produtos(Request $request): JsonResponse
    {
        $produtos = Produto::orderBy('nome')->get(['id', 'nome', 'ativo']);

        return response()->json($produtos);
    }
}
