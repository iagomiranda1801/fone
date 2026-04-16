<template>
  <div class="flex gap-6 text-sm font-medium py-2">
    <div class="flex items-center gap-2">
      <span class="text-gray-600">Total da Venda:</span>
      <span class="text-lg font-bold text-indigo-700">R$ {{ total.toFixed(2) }}</span>
    </div>
    <div class="flex items-center gap-2">
      <span class="text-gray-600">Lucro Estimado:</span>
      <span
        class="text-lg font-bold"
        :class="lucroEstimado >= 0 ? 'text-emerald-600' : 'text-red-600'"
      >
        R$ {{ lucroEstimado.toFixed(2) }}
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  total: { type: Number, default: 0 },
  lucroEstimado: { type: Number, default: 0 },
})
</script>