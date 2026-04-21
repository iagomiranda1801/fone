<template>
  <div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Relatórios</h1>
        <p class="text-sm text-gray-500 mt-0.5">Análise e exportação de dados</p>
      </div>
    </div>

    <!-- Tab Bar -->
    <div class="flex gap-1 p-1 bg-gray-100 rounded-2xl w-full overflow-x-auto">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold whitespace-nowrap transition-all duration-200 flex-1 justify-center"
        :class="activeTab === tab.id
          ? 'bg-white text-gray-900 shadow-sm ' + tab.activeText
          : 'text-gray-500 hover:text-gray-700'"
      >
        <div class="w-6 h-6 rounded-lg flex items-center justify-center" :class="activeTab === tab.id ? tab.iconBg : 'bg-transparent'">
          <svg class="w-3.5 h-3.5" :class="activeTab === tab.id ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="tab.icon"></svg>
        </div>
        {{ tab.label }}
      </button>
    </div>

    <!-- VENDAS TAB -->
    <template v-if="activeTab === 'vendas'">
      <!-- Filters -->
      <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-1 h-5 rounded-full bg-gradient-to-b from-blue-500 to-cyan-500"></div>
          <h3 class="text-sm font-bold text-gray-800">Filtros</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Data Início</label>
            <input v-model="filtrosVendas.data_inicio" type="date" class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Data Fim</label>
            <input v-model="filtrosVendas.data_fim" type="date" class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cliente</label>
            <input v-model="filtrosVendas.cliente" type="text" placeholder="Buscar..." class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Produto</label>
            <select v-model="filtrosVendas.produto_id" class="input-base w-full">
              <option value="">Todos</option>
              <option v-for="p in produtosLista" :key="p.id" :value="p.id">{{ p.nome }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select v-model="filtrosVendas.status" class="input-base w-full">
              <option value="">Todos</option>
              <option value="ativa">Ativas</option>
              <option value="cancelada">Canceladas</option>
            </select>
          </div>
        </div>
        <div class="flex items-center gap-2 mt-4">
          <button @click="buscarVendas" :disabled="loadingVendas" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            {{ loadingVendas ? 'Buscando...' : 'Buscar' }}
          </button>
          <button @click="limparFiltros('vendas')" class="btn-secondary">Limpar</button>
          <div class="ml-auto flex items-center gap-2" v-if="vendas.data.length > 0">
            <button @click="exportarVendasExcel" class="btn-excel">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Excel
            </button>
            <button @click="exportarVendasPDF" class="btn-pdf">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Summary cards -->
      <div v-if="vendas.data.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <SummaryCard label="Total de Vendas" :value="vendas.totalizadores.total_vendas" color="blue" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>' />
        <SummaryCard label="Faturamento" :value="money(vendas.totalizadores.faturamento)" color="green" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
        <SummaryCard label="Lucro Total" :value="money(vendas.totalizadores.lucro_total)" color="indigo" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>' />
        <SummaryCard label="Canceladas" :value="vendas.totalizadores.vendas_canceladas" color="red" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
      </div>

      <!-- Table -->
      <ReportTable
        v-if="vendas.data.length > 0"
        :columns="['ID','Cliente','Itens','Total','Lucro','Status','Data']"
        :rows="vendas.data"
        :loading="loadingVendas"
        empty-message="Nenhuma venda encontrada com os filtros selecionados."
      >
        <template #row="{ row }">
          <td class="px-4 py-3 text-sm text-gray-500 font-mono">#{{ row.id }}</td>
          <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ row.cliente }}</td>
          <td class="px-4 py-3 text-sm text-center">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ row.itens.length }}</span>
          </td>
          <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ money(row.total) }}</td>
          <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">{{ money(row.lucro) }}</td>
          <td class="px-4 py-3 text-sm text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold"
              :class="row.cancelada ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'">
              {{ row.cancelada ? 'Cancelada' : 'Ativa' }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ row.created_at }}</td>
        </template>
      </ReportTable>
      <EmptyState v-else-if="!loadingVendas && buscouVendas" message="Nenhuma venda encontrada." />
    </template>

    <!-- COMPRAS TAB -->
    <template v-if="activeTab === 'compras'">
      <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-1 h-5 rounded-full bg-gradient-to-b from-emerald-500 to-teal-500"></div>
          <h3 class="text-sm font-bold text-gray-800">Filtros</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Data Início</label>
            <input v-model="filtrosCompras.data_inicio" type="date" class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Data Fim</label>
            <input v-model="filtrosCompras.data_fim" type="date" class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Fornecedor</label>
            <input v-model="filtrosCompras.fornecedor" type="text" placeholder="Buscar..." class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Produto</label>
            <select v-model="filtrosCompras.produto_id" class="input-base w-full">
              <option value="">Todos</option>
              <option v-for="p in produtosLista" :key="p.id" :value="p.id">{{ p.nome }}</option>
            </select>
          </div>
        </div>
        <div class="flex items-center gap-2 mt-4">
          <button @click="buscarCompras" :disabled="loadingCompras" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            {{ loadingCompras ? 'Buscando...' : 'Buscar' }}
          </button>
          <button @click="limparFiltros('compras')" class="btn-secondary">Limpar</button>
          <div class="ml-auto flex items-center gap-2" v-if="compras.data.length > 0">
            <button @click="exportarComprasExcel" class="btn-excel">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Excel
            </button>
            <button @click="exportarComprasPDF" class="btn-pdf">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              PDF
            </button>
          </div>
        </div>
      </div>

      <div v-if="compras.data.length > 0" class="grid grid-cols-2 gap-3">
        <SummaryCard label="Total de Compras" :value="compras.totalizadores.total_compras" color="green" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>' />
        <SummaryCard label="Valor Total Comprado" :value="money(compras.totalizadores.valor_total)" color="teal" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
      </div>

      <ReportTable
        v-if="compras.data.length > 0"
        :columns="['ID','Fornecedor','Itens','Total','Data']"
        :loading="loadingCompras"
        :rows="compras.data"
      >
        <template #row="{ row }">
          <td class="px-4 py-3 text-sm text-gray-500 font-mono">#{{ row.id }}</td>
          <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ row.fornecedor }}</td>
          <td class="px-4 py-3 text-sm text-center">
            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">{{ row.itens.length }}</span>
          </td>
          <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ money(row.total) }}</td>
          <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ row.created_at }}</td>
        </template>
      </ReportTable>
      <EmptyState v-else-if="!loadingCompras && buscouCompras" message="Nenhuma compra encontrada." />
    </template>

    <!-- ESTOQUE TAB -->
    <template v-if="activeTab === 'estoque'">
      <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-1 h-5 rounded-full bg-gradient-to-b from-amber-500 to-orange-500"></div>
          <h3 class="text-sm font-bold text-gray-800">Filtros</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Nome do Produto</label>
            <input v-model="filtrosEstoque.nome" type="text" placeholder="Buscar..." class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select v-model="filtrosEstoque.status" class="input-base w-full">
              <option value="">Todos</option>
              <option value="ativo">Ativos</option>
              <option value="inativo">Inativos</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Nível de Estoque</label>
            <select v-model="filtrosEstoque.estoque_nivel" class="input-base w-full">
              <option value="">Todos</option>
              <option value="zerado">Zerado</option>
              <option value="baixo">Baixo (≤ 5)</option>
              <option value="normal">Normal (> 5)</option>
            </select>
          </div>
        </div>
        <div class="flex items-center gap-2 mt-4">
          <button @click="buscarEstoque" :disabled="loadingEstoque" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            {{ loadingEstoque ? 'Buscando...' : 'Buscar' }}
          </button>
          <button @click="limparFiltros('estoque')" class="btn-secondary">Limpar</button>
          <div class="ml-auto flex items-center gap-2" v-if="estoque.data.length > 0">
            <button @click="exportarEstoqueExcel" class="btn-excel">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Excel
            </button>
            <button @click="exportarEstoquePDF" class="btn-pdf">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              PDF
            </button>
          </div>
        </div>
      </div>

      <div v-if="estoque.data.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <SummaryCard label="Total Produtos" :value="estoque.totalizadores.total_produtos" color="amber" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>' />
        <SummaryCard label="Valor em Estoque" :value="money(estoque.totalizadores.valor_total_estoque)" color="amber" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
        <SummaryCard label="Estoque Zerado" :value="estoque.totalizadores.produtos_zerados" color="red" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>' />
        <SummaryCard label="Estoque Baixo" :value="estoque.totalizadores.produtos_baixo" color="orange" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>' />
      </div>

      <ReportTable
        v-if="estoque.data.length > 0"
        :columns="['ID','Produto','Qtd','Custo Médio','Pr. Venda','Margem','Valor Estoque','Status','Nível']"
        :loading="loadingEstoque"
        :rows="estoque.data"
      >
        <template #row="{ row }">
          <td class="px-4 py-3 text-sm text-gray-500 font-mono">#{{ row.id }}</td>
          <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ row.nome }}</td>
          <td class="px-4 py-3 text-sm text-center font-bold" :class="row.estoque === 0 ? 'text-red-600' : row.estoque <= 5 ? 'text-amber-600' : 'text-gray-700'">{{ row.estoque }}</td>
          <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ money(row.custo_medio) }}</td>
          <td class="px-4 py-3 text-sm text-gray-800 font-semibold text-right">{{ money(row.preco_venda) }}</td>
          <td class="px-4 py-3 text-sm text-right font-bold" :class="row.margem >= 30 ? 'text-emerald-600' : row.margem >= 10 ? 'text-amber-600' : 'text-red-600'">{{ row.margem }}%</td>
          <td class="px-4 py-3 text-sm font-bold text-indigo-700 text-right">{{ money(row.valor_estoque) }}</td>
          <td class="px-4 py-3 text-sm text-center">
            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold" :class="row.ativo ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'">
              {{ row.ativo ? 'Ativo' : 'Inativo' }}
            </span>
          </td>
          <td class="px-4 py-3 text-sm text-center">
            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold"
              :class="row.nivel === 'zerado' ? 'bg-red-100 text-red-700' : row.nivel === 'baixo' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'">
              {{ row.nivel === 'zerado' ? 'Zerado' : row.nivel === 'baixo' ? 'Baixo' : 'Normal' }}
            </span>
          </td>
        </template>
      </ReportTable>
      <EmptyState v-else-if="!loadingEstoque && buscouEstoque" message="Nenhum produto encontrado." />
    </template>

    <!-- LUCRATIVIDADE TAB -->
    <template v-if="activeTab === 'lucratividade'">
      <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-1 h-5 rounded-full bg-gradient-to-b from-violet-500 to-purple-500"></div>
          <h3 class="text-sm font-bold text-gray-800">Filtros</h3>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Data Início</label>
            <input v-model="filtrosLucratividade.data_inicio" type="date" class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Data Fim</label>
            <input v-model="filtrosLucratividade.data_fim" type="date" class="input-base w-full" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Produto</label>
            <select v-model="filtrosLucratividade.produto_id" class="input-base w-full">
              <option value="">Todos</option>
              <option v-for="p in produtosLista" :key="p.id" :value="p.id">{{ p.nome }}</option>
            </select>
          </div>
        </div>
        <div class="flex items-center gap-2 mt-4">
          <button @click="buscarLucratividade" :disabled="loadingLucratividade" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            {{ loadingLucratividade ? 'Buscando...' : 'Buscar' }}
          </button>
          <button @click="limparFiltros('lucratividade')" class="btn-secondary">Limpar</button>
          <div class="ml-auto flex items-center gap-2" v-if="lucratividade.data.length > 0">
            <button @click="exportarLucratividadeExcel" class="btn-excel">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Excel
            </button>
            <button @click="exportarLucratividadePDF" class="btn-pdf">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
              PDF
            </button>
          </div>
        </div>
      </div>

      <div v-if="lucratividade.data.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <SummaryCard label="Receita Total" :value="money(lucratividade.totalizadores.receita_total)" color="indigo" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' />
        <SummaryCard label="Custo Total" :value="money(lucratividade.totalizadores.custo_total)" color="red" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>' />
        <SummaryCard label="Lucro Total" :value="money(lucratividade.totalizadores.lucro_total)" color="green" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>' />
        <SummaryCard label="Margem Média" :value="lucratividade.totalizadores.margem_media + '%'" color="violet" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>' />
      </div>

      <ReportTable
        v-if="lucratividade.data.length > 0"
        :columns="['#','Produto','Qtd Vendida','Receita','Custo','Lucro','Margem']"
        :loading="loadingLucratividade"
        :rows="lucratividade.data"
      >
        <template #row="{ row, index }">
          <td class="px-4 py-3 text-sm font-bold text-gray-500">{{ index + 1 }}º</td>
          <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ row.nome }}</td>
          <td class="px-4 py-3 text-sm text-center font-bold text-indigo-600">{{ row.total_vendido }}</td>
          <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">{{ money(row.receita) }}</td>
          <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ money(row.custo) }}</td>
          <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">{{ money(row.lucro) }}</td>
          <td class="px-4 py-3 text-sm font-bold text-right" :class="row.margem >= 30 ? 'text-emerald-600' : row.margem >= 10 ? 'text-amber-600' : 'text-red-600'">
            {{ row.margem }}%
          </td>
        </template>
      </ReportTable>
      <EmptyState v-else-if="!loadingLucratividade && buscouLucratividade" message="Nenhum dado de lucratividade encontrado." />
    </template>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import {
  getRelatorioVendas,
  getRelatorioCompras,
  getRelatorioEstoque,
  getRelatorioLucratividade,
  getProdutosLista,
} from '../services/relatorioService'
import {
  exportVendasExcel, exportVendasPDF,
  exportComprasExcel, exportComprasPDF,
  exportEstoqueExcel, exportEstoquePDF,
  exportLucratividadeExcel, exportLucratividadePDF,
} from '../utils/exportUtils'
import SummaryCard from '../components/relatorios/SummaryCard.vue'
import ReportTable from '../components/relatorios/ReportTable.vue'
import EmptyState from '../components/relatorios/EmptyState.vue'

// ─── tabs ────────────────────────────────────────────────────────────────────
const activeTab = ref('vendas')
const tabs = [
  {
    id: 'vendas', label: 'Vendas',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"/>',
    iconBg: 'bg-blue-500', activeText: 'text-blue-700',
  },
  {
    id: 'compras', label: 'Compras',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>',
    iconBg: 'bg-emerald-500', activeText: 'text-emerald-700',
  },
  {
    id: 'estoque', label: 'Estoque',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
    iconBg: 'bg-amber-500', activeText: 'text-amber-700',
  },
  {
    id: 'lucratividade', label: 'Lucratividade',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
    iconBg: 'bg-violet-500', activeText: 'text-violet-700',
  },
]

// ─── data ────────────────────────────────────────────────────────────────────
const produtosLista = ref([])

const vendas        = reactive({ data: [], totalizadores: {} })
const compras       = reactive({ data: [], totalizadores: {} })
const estoque       = reactive({ data: [], totalizadores: {} })
const lucratividade = reactive({ data: [], totalizadores: {} })

const loadingVendas        = ref(false)
const loadingCompras       = ref(false)
const loadingEstoque       = ref(false)
const loadingLucratividade = ref(false)

const buscouVendas        = ref(false)
const buscouCompras       = ref(false)
const buscouEstoque       = ref(false)
const buscouLucratividade = ref(false)

// ─── filters ─────────────────────────────────────────────────────────────────
const filtrosVendas = reactive({ data_inicio: '', data_fim: '', cliente: '', produto_id: '', status: '' })
const filtrosCompras = reactive({ data_inicio: '', data_fim: '', fornecedor: '', produto_id: '' })
const filtrosEstoque = reactive({ nome: '', status: '', estoque_nivel: '' })
const filtrosLucratividade = reactive({ data_inicio: '', data_fim: '', produto_id: '' })

function filtrosAtivosStr(filtros) {
  return Object.entries(filtros)
    .filter(([, v]) => v)
    .map(([k, v]) => `${k}: ${v}`)
    .join(' | ') || 'Sem filtros'
}

function limparFiltros(tipo) {
  if (tipo === 'vendas') Object.keys(filtrosVendas).forEach(k => filtrosVendas[k] = '')
  if (tipo === 'compras') Object.keys(filtrosCompras).forEach(k => filtrosCompras[k] = '')
  if (tipo === 'estoque') Object.keys(filtrosEstoque).forEach(k => filtrosEstoque[k] = '')
  if (tipo === 'lucratividade') Object.keys(filtrosLucratividade).forEach(k => filtrosLucratividade[k] = '')
}

// ─── fetch ───────────────────────────────────────────────────────────────────
async function buscarVendas() {
  loadingVendas.value = true
  try {
    const { data } = await getRelatorioVendas(filtrosAtivos(filtrosVendas))
    vendas.data = data.data
    vendas.totalizadores = data.totalizadores
    buscouVendas.value = true
  } finally {
    loadingVendas.value = false
  }
}

async function buscarCompras() {
  loadingCompras.value = true
  try {
    const { data } = await getRelatorioCompras(filtrosAtivos(filtrosCompras))
    compras.data = data.data
    compras.totalizadores = data.totalizadores
    buscouCompras.value = true
  } finally {
    loadingCompras.value = false
  }
}

async function buscarEstoque() {
  loadingEstoque.value = true
  try {
    const { data } = await getRelatorioEstoque(filtrosAtivos(filtrosEstoque))
    estoque.data = data.data
    estoque.totalizadores = data.totalizadores
    buscouEstoque.value = true
  } finally {
    loadingEstoque.value = false
  }
}

async function buscarLucratividade() {
  loadingLucratividade.value = true
  try {
    const { data } = await getRelatorioLucratividade(filtrosAtivos(filtrosLucratividade))
    lucratividade.data = data.data
    lucratividade.totalizadores = data.totalizadores
    buscouLucratividade.value = true
  } finally {
    loadingLucratividade.value = false
  }
}

function filtrosAtivos(obj) {
  return Object.fromEntries(Object.entries(obj).filter(([, v]) => v !== ''))
}

// ─── exports ─────────────────────────────────────────────────────────────────
function exportarVendasExcel()        { exportVendasExcel(vendas.data, vendas.totalizadores) }
function exportarVendasPDF()          { exportVendasPDF(vendas.data, vendas.totalizadores, filtrosAtivosStr(filtrosVendas)) }
function exportarComprasExcel()       { exportComprasExcel(compras.data, compras.totalizadores) }
function exportarComprasPDF()         { exportComprasPDF(compras.data, compras.totalizadores, filtrosAtivosStr(filtrosCompras)) }
function exportarEstoqueExcel()       { exportEstoqueExcel(estoque.data, estoque.totalizadores) }
function exportarEstoquePDF()         { exportEstoquePDF(estoque.data, estoque.totalizadores, filtrosAtivosStr(filtrosEstoque)) }
function exportarLucratividadeExcel() { exportLucratividadeExcel(lucratividade.data, lucratividade.totalizadores) }
function exportarLucratividadePDF()   { exportLucratividadePDF(lucratividade.data, lucratividade.totalizadores, filtrosAtivosStr(filtrosLucratividade)) }

// ─── helpers ─────────────────────────────────────────────────────────────────
function money(val) {
  return Number(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

onMounted(async () => {
  const { data } = await getProdutosLista()
  produtosLista.value = data
  // Load defaults
  await Promise.all([buscarVendas(), buscarEstoque()])
})
</script>

<style scoped>
.input-base {
  @apply block w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-sm text-gray-800
    focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all duration-200;
}
.btn-primary {
  @apply inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600
    text-white text-sm font-semibold shadow-sm hover:shadow-md hover:from-indigo-600 hover:to-purple-700
    disabled:opacity-60 transition-all duration-200;
}
.btn-secondary {
  @apply inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 text-gray-600 text-sm font-semibold
    hover:bg-gray-200 transition-all duration-200;
}
.btn-excel {
  @apply inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-500 text-white text-xs font-bold
    hover:bg-emerald-600 shadow-sm transition-all duration-200;
}
.btn-pdf {
  @apply inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-red-500 text-white text-xs font-bold
    hover:bg-red-600 shadow-sm transition-all duration-200;
}
</style>
