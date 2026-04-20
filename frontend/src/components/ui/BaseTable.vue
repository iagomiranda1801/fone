<template>
  <div class="overflow-x-auto rounded-xl border border-gray-200/60">
    <table class="min-w-full divide-y divide-gray-200/60">
      <thead class="bg-gradient-to-r from-gray-50 to-gray-50/50">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            class="px-4 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider"
          >
            {{ col.label }}
          </th>
          <th v-if="$slots.actions" class="px-4 py-3.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
            Ações
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        <tr v-if="rows.length === 0">
          <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-12 text-center">
            <div class="flex flex-col items-center gap-2">
              <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-sm text-gray-400 font-medium">{{ emptyText }}</p>
            </div>
          </td>
        </tr>
        <tr v-for="(row, index) in rows" :key="row.id || index" class="hover:bg-indigo-50/30 transition-colors duration-150">
          <td
            v-for="col in columns"
            :key="col.key"
            class="px-4 py-3.5 text-sm text-gray-700 whitespace-nowrap"
          >
            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
              {{ formatValue(row[col.key], col) }}
            </slot>
          </td>
          <td v-if="$slots.actions" class="px-4 py-3.5 text-right text-sm whitespace-nowrap">
            <slot name="actions" :row="row" :index="index" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, default: () => [] },
  emptyText: { type: String, default: 'Nenhum registro encontrado.' },
})

function formatValue(value, col) {
  if (value == null) return '—'
  if (col.format === 'currency') return `R$ ${Number(value).toFixed(2)}`
  return value
}
</script>
