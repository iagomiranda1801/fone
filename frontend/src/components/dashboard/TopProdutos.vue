<template>
  <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-1 h-6 rounded-full bg-gradient-to-b from-blue-500 to-cyan-500"></div>
      <h3 class="text-sm font-bold text-gray-800">Produtos Mais Vendidos</h3>
    </div>

    <div v-if="produtos.length === 0" class="text-center py-6">
      <p class="text-sm text-gray-400">Nenhuma venda registrada ainda.</p>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="(p, idx) in produtos"
        :key="p.id"
        class="group relative"
      >
        <div class="flex items-center gap-3">
          <div
            class="flex items-center justify-center w-8 h-8 rounded-lg text-xs font-extrabold text-white shadow-sm"
            :class="rankBg(idx)"
          >
            {{ idx + 1 }}º
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ p.nome }}</p>
              <p class="text-xs font-bold text-gray-500 ml-2 whitespace-nowrap">{{ p.total_vendido }} un.</p>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-700"
                :class="rankBar(idx)"
                :style="{ width: barWidth(p.total_vendido) }"
              ></div>
            </div>
          </div>
          <p class="text-xs font-bold text-indigo-600 whitespace-nowrap ml-2">
            {{ money(p.receita) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  produtos: { type: Array, required: true },
})

function money(val) {
  return Number(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

const maxQtd = computed(() => {
  return Math.max(...props.produtos.map(p => p.total_vendido), 1)
})

function barWidth(qty) {
  return `${(qty / maxQtd.value) * 100}%`
}

function rankBg(idx) {
  const colors = [
    'bg-gradient-to-br from-amber-400 to-amber-600',
    'bg-gradient-to-br from-gray-400 to-gray-500',
    'bg-gradient-to-br from-orange-400 to-orange-600',
    'bg-gradient-to-br from-indigo-400 to-indigo-500',
    'bg-gradient-to-br from-blue-400 to-blue-500',
  ]
  return colors[idx] || colors[4]
}

function rankBar(idx) {
  const colors = [
    'bg-gradient-to-r from-amber-400 to-amber-500',
    'bg-gradient-to-r from-gray-300 to-gray-400',
    'bg-gradient-to-r from-orange-300 to-orange-400',
    'bg-gradient-to-r from-indigo-300 to-indigo-400',
    'bg-gradient-to-r from-blue-300 to-blue-400',
  ]
  return colors[idx] || colors[4]
}
</script>
