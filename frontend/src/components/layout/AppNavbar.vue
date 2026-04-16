<template>
  <nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center gap-2">
          <span class="text-xl font-bold text-indigo-600">ERP</span>
          <span class="text-xl font-light text-gray-500">Estoque</span>
        </div>
        <div class="flex gap-1">
          <RouterLink
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="px-4 py-2 rounded-md text-sm font-medium transition-colors"
            :class="
              $route.path === link.to
                ? 'bg-indigo-50 text-indigo-700'
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
            "
          >
            {{ link.label }}
          </RouterLink>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { RouterLink } from 'vue-router'

const links = [
  { to: '/produtos', label: 'Produtos' },
  { to: '/compras', label: 'Compras' },
  { to: '/vendas', label: 'Vendas' },
]
</script>
