<template>
  <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-5">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-1 h-6 rounded-full" :class="accentClass"></div>
      <h3 class="text-sm font-bold text-gray-800">{{ title }}</h3>
    </div>

    <div v-if="items.length === 0" class="text-center py-6">
      <p class="text-sm text-gray-400">Nenhum registro encontrado.</p>
    </div>

    <div v-else class="space-y-2">
      <RouterLink
        v-for="item in items"
        :key="item.id"
        :to="linkTo"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition-colors duration-150 cursor-pointer group"
      >
        <div class="flex items-center justify-center w-8 h-8 rounded-lg text-[11px] font-bold" :class="idClass">
          #{{ item.id }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-gray-800 truncate">{{ item.nome }}</p>
          <div class="flex items-center gap-2 text-[11px] text-gray-400">
            <span>{{ item.itens_count }} {{ item.itens_count === 1 ? 'item' : 'itens' }}</span>
            <span>·</span>
            <span>{{ formatDate(item.created_at) }}</span>
          </div>
        </div>
        <div class="text-right">
          <p class="text-sm font-bold" :class="valueClass">{{ money(item.total) }}</p>
          <p v-if="item.lucro !== undefined && !item.cancelada" class="text-[10px] text-emerald-600 font-semibold">
            +{{ money(item.lucro) }} lucro
          </p>
          <span v-if="item.cancelada" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600">
            Cancelada
          </span>
        </div>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'

defineProps({
  title: { type: String, required: true },
  items: { type: Array, required: true },
  linkTo: { type: String, default: '/' },
  accentClass: { type: String, default: 'bg-gradient-to-b from-indigo-500 to-purple-500' },
  idClass: { type: String, default: 'bg-indigo-50 text-indigo-600' },
  valueClass: { type: String, default: 'text-gray-800' },
})

function money(val) {
  return Number(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' })
}
</script>
