<template>
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <RouterLink
      v-for="action in actions"
      :key="action.to"
      :to="action.to"
      class="group relative overflow-hidden rounded-2xl p-4 bg-white border border-gray-200/60 shadow-sm hover:shadow-lg hover:border-indigo-200 transition-all duration-300"
    >
      <div
        class="flex items-center justify-center w-11 h-11 rounded-xl mb-3 transition-transform duration-300 group-hover:scale-110"
        :class="action.iconBg"
      >
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="action.icon"></svg>
      </div>
      <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">{{ action.label }}</p>
      <p class="text-[11px] text-gray-400 mt-0.5">{{ action.description }}</p>
      <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-1 group-hover:translate-x-0">
        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </div>
    </RouterLink>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'

const actions = [
  {
    to: '/produtos',
    label: 'Gerenciar Produtos',
    description: 'Cadastrar, ativar e acompanhar estoque',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
    iconBg: 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-md shadow-amber-500/20',
  },
  {
    to: '/compras',
    label: 'Nova Compra',
    description: 'Registrar entrada de mercadorias',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />',
    iconBg: 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md shadow-emerald-500/20',
  },
  {
    to: '/vendas',
    label: 'Nova Venda',
    description: 'Registrar saída e faturar',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z" />',
    iconBg: 'bg-gradient-to-br from-blue-500 to-cyan-600 shadow-md shadow-blue-500/20',
  },
  {
    to: '/produtos',
    label: 'Verificar Estoque',
    description: 'Acompanhar níveis e custos',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
    iconBg: 'bg-gradient-to-br from-indigo-500 to-purple-600 shadow-md shadow-indigo-500/20',
  },
]
</script>
