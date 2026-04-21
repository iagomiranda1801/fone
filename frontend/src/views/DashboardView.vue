<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Visão geral do seu negócio</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          {{ today }}
        </span>
        <button
          @click="handleRefresh"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all duration-200"
          :class="{ 'animate-spin-once': refreshing }"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Atualizar
        </button>
      </div>
    </div>

    <PageLoading :loading="pageLoading" message="Carregando dashboard..." />

    <template v-if="!pageLoading && data">
      <!-- KPI Cards -->
      <KpiCards :resumo="data.resumo" />

      <!-- Quick Actions -->
      <QuickActions />

      <!-- Chart + Top Produtos -->
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-3">
          <WeeklyChart :dias="data.grafico_7dias" />
        </div>
        <div class="lg:col-span-2">
          <TopProdutos :produtos="data.top_produtos" />
        </div>
      </div>

      <!-- Recent Lists + Stock Alerts -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <RecentList
          title="Últimas Vendas"
          :items="vendasFormatted"
          link-to="/vendas"
          accent-class="bg-gradient-to-b from-blue-500 to-cyan-500"
          id-class="bg-blue-50 text-blue-600"
          value-class="text-blue-700"
        />
        <RecentList
          title="Últimas Compras"
          :items="comprasFormatted"
          link-to="/compras"
          accent-class="bg-gradient-to-b from-emerald-500 to-teal-500"
          id-class="bg-emerald-50 text-emerald-600"
          value-class="text-emerald-700"
        />
        <EstoqueCritico :produtos="data.estoque_critico" />
      </div>
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useDashboard } from '../composables/useDashboard'
import PageLoading from '../components/ui/PageLoading.vue'
import KpiCards from '../components/dashboard/KpiCards.vue'
import QuickActions from '../components/dashboard/QuickActions.vue'
import WeeklyChart from '../components/dashboard/WeeklyChart.vue'
import TopProdutos from '../components/dashboard/TopProdutos.vue'
import EstoqueCritico from '../components/dashboard/EstoqueCritico.vue'
import RecentList from '../components/dashboard/RecentList.vue'

const { data, carregar } = useDashboard()
const pageLoading = ref(true)
const refreshing = ref(false)

const today = new Date().toLocaleDateString('pt-BR', {
  day: '2-digit',
  month: 'long',
  year: 'numeric',
})

const vendasFormatted = computed(() =>
  (data.value?.ultimas_vendas || []).map((v) => ({
    id: v.id,
    nome: v.cliente,
    total: v.total,
    lucro: v.lucro,
    cancelada: v.cancelada,
    itens_count: v.itens_count,
    created_at: v.created_at,
  }))
)

const comprasFormatted = computed(() =>
  (data.value?.ultimas_compras || []).map((c) => ({
    id: c.id,
    nome: c.fornecedor,
    total: c.total,
    itens_count: c.itens_count,
    created_at: c.created_at,
  }))
)

onMounted(async () => {
  try {
    await carregar()
  } finally {
    pageLoading.value = false
  }
})

async function handleRefresh() {
  refreshing.value = true
  try {
    await carregar()
  } finally {
    refreshing.value = false
  }
}
</script>
