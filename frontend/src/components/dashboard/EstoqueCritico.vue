<template>
  <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <div class="w-1 h-6 rounded-full bg-gradient-to-b from-red-500 to-rose-500"></div>
        <h3 class="text-sm font-bold text-gray-800">Estoque Crítico</h3>
      </div>
      <span
        v-if="produtos.length > 0"
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600"
      >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        {{ produtos.length }} produto{{ produtos.length !== 1 ? 's' : '' }}
      </span>
    </div>

    <div v-if="produtos.length === 0" class="text-center py-6">
      <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 mx-auto mb-2">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <p class="text-sm text-gray-400 font-medium">Estoque saudável!</p>
    </div>

    <div v-else class="space-y-2 max-h-64 overflow-y-auto pr-1">
      <div
        v-for="p in produtos"
        :key="p.id"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-red-50/50 transition-colors duration-150"
      >
        <div
          class="flex items-center justify-center w-8 h-8 rounded-lg text-xs font-extrabold"
          :class="p.estoque === 0 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600'"
        >
          {{ p.estoque }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-gray-800 truncate">{{ p.nome }}</p>
          <p class="text-[11px] text-gray-400">
            Custo {{ money(p.custo_medio) }} · Venda {{ money(p.preco_venda) }}
          </p>
        </div>
        <span
          class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
          :class="p.estoque === 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'"
        >
          {{ p.estoque === 0 ? 'Zerado' : 'Baixo' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  produtos: { type: Array, required: true },
})

function money(val) {
  return Number(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}
</script>
