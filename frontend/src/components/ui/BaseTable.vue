<template>
  <div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
          >
            {{ col.label }}
          </th>
          <th v-if="$slots.actions" class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
            Ações
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-if="rows.length === 0">
          <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-8 text-center text-sm text-gray-500">
            {{ emptyText }}
          </td>
        </tr>
        <tr v-for="(row, index) in rows" :key="row.id || index" class="hover:bg-gray-50">
          <td
            v-for="col in columns"
            :key="col.key"
            class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap"
          >
            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
              {{ formatValue(row[col.key], col) }}
            </slot>
          </td>
          <td v-if="$slots.actions" class="px-4 py-3 text-right text-sm whitespace-nowrap">
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
