<template>
  <div class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-gray-200/60 shadow-sm">
    <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gray-100 mb-3">
      <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
    </div>
    <p class="text-sm font-semibold text-gray-400">{{ message }}</p>
    <p class="text-xs text-gray-300 mt-1">Tente ajustar os filtros acima.</p>
  </div>
</template>

<script setup>
defineProps({ message: { type: String, default: 'Nenhum dado encontrado.' } })
</script>
