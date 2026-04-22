<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // produtos
        Schema::table('produtos', function (Blueprint $table) {
            $table->index('nome', 'idx_produtos_nome');
            $table->index('ativo', 'idx_produtos_ativo');
            $table->index('estoque', 'idx_produtos_estoque');
        });

        // vendas
        Schema::table('vendas', function (Blueprint $table) {
            $table->index('created_at', 'idx_vendas_created_at');
            $table->index('cancelada', 'idx_vendas_cancelada');
            $table->index('cliente', 'idx_vendas_cliente');
        });

        // venda_items
        Schema::table('venda_items', function (Blueprint $table) {
            $table->index('produto_id', 'idx_venda_items_produto_id');
            $table->index('venda_id', 'idx_venda_items_venda_id');
        });

        // compras
        Schema::table('compras', function (Blueprint $table) {
            $table->index('created_at', 'idx_compras_created_at');
            $table->index('fornecedor', 'idx_compras_fornecedor');
        });

        // compra_items
        Schema::table('compra_items', function (Blueprint $table) {
            $table->index('produto_id', 'idx_compra_items_produto_id');
            $table->index('compra_id', 'idx_compra_items_compra_id');
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropIndex('idx_produtos_nome');
            $table->dropIndex('idx_produtos_ativo');
            $table->dropIndex('idx_produtos_estoque');
        });

        Schema::table('vendas', function (Blueprint $table) {
            $table->dropIndex('idx_vendas_created_at');
            $table->dropIndex('idx_vendas_cancelada');
            $table->dropIndex('idx_vendas_cliente');
        });

        Schema::table('venda_items', function (Blueprint $table) {
            $table->dropIndex('idx_venda_items_produto_id');
            $table->dropIndex('idx_venda_items_venda_id');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropIndex('idx_compras_created_at');
            $table->dropIndex('idx_compras_fornecedor');
        });

        Schema::table('compra_items', function (Blueprint $table) {
            $table->dropIndex('idx_compra_items_produto_id');
            $table->dropIndex('idx_compra_items_compra_id');
        });
    }
};
