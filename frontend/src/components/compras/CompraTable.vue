<template>
  <BaseTable :columns="columns" :rows="compras" empty-text="Nenhuma compra registrada.">
    <template #cell-itens="{ row }">
      <ul class="text-xs space-y-0.5">
        <li v-for="item in row.itens" :key="item.id">
          {{ item.produto?.nome }} — {{ item.quantidade }}x R$ {{ Number(item.preco_unitario).toFixed(2) }}
        </li>
      </ul>
    </template>
    <template #cell-created_at="{ value }">
      {{ formatDate(value) }}
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../ui/BaseTable.vue'

defineProps({
  compras: { type: Array, default: () => [] },
})

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'fornecedor', label: 'Fornecedor' },
  { key: 'itens', label: 'Itens' },
  { key: 'created_at', label: 'Data' },
]

function formatDate(value) {
  if (!value) return '—'
  return new Date(value).toLocaleDateString('pt-BR')
}
</script>
