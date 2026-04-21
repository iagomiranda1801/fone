<template>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
    <div
      v-for="card in cards"
      :key="card.label"
      class="relative overflow-hidden rounded-2xl p-4 bg-white border border-gray-200/60 shadow-sm hover:shadow-md transition-all duration-300 group"
    >
      <div class="flex items-start justify-between mb-3">
        <div
          class="flex items-center justify-center w-10 h-10 rounded-xl shadow-md transition-transform duration-300 group-hover:scale-110"
          :class="card.iconBg"
        >
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="card.icon"></svg>
        </div>
        <span
          v-if="card.badge"
          class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
          :class="card.badgeClass"
        >
          {{ card.badge }}
        </span>
      </div>
      <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ card.value }}</p>
      <p class="text-xs text-gray-400 font-medium mt-0.5">{{ card.label }}</p>
      <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full opacity-[0.04]" :class="card.bgCircle"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  resumo: { type: Object, required: true },
})

function money(val) {
  return Number(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

const cards = computed(() => [
  {
    label: 'Faturamento',
    value: money(props.resumo.faturamento),
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    iconBg: 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-emerald-500/25',
    bgCircle: 'bg-emerald-500',
    badge: props.resumo.margem_lucro > 0 ? `${props.resumo.margem_lucro}% margem` : null,
    badgeClass: 'bg-emerald-50 text-emerald-700',
  },
  {
    label: 'Lucro Total',
    value: money(props.resumo.lucro_total),
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
    iconBg: 'bg-gradient-to-br from-indigo-500 to-purple-600 shadow-indigo-500/25',
    bgCircle: 'bg-indigo-500',
    badge: null,
    badgeClass: '',
  },
  {
    label: 'Valor em Estoque',
    value: money(props.resumo.valor_estoque),
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
    iconBg: 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/25',
    bgCircle: 'bg-amber-500',
    badge: `${props.resumo.produtos_ativos} ativos`,
    badgeClass: 'bg-amber-50 text-amber-700',
  },
  {
    label: 'Total Vendas',
    value: props.resumo.vendas_ativas,
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" />',
    iconBg: 'bg-gradient-to-br from-blue-500 to-cyan-600 shadow-blue-500/25',
    bgCircle: 'bg-blue-500',
    badge: props.resumo.vendas_canceladas > 0 ? `${props.resumo.vendas_canceladas} canceladas` : null,
    badgeClass: 'bg-red-50 text-red-600',
  },
  {
    label: 'Ticket Médio',
    value: money(props.resumo.ticket_medio),
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />',
    iconBg: 'bg-gradient-to-br from-pink-500 to-rose-600 shadow-pink-500/25',
    bgCircle: 'bg-pink-500',
    badge: null,
    badgeClass: '',
  },
])
</script>
