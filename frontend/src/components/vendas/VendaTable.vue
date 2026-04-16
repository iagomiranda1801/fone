<template>
  <BaseTable :columns="columns" :rows="vendas" empty-text="Nenhuma venda registrada.">
    <template #cell-total="{ value }">
      R$ {{ Number(value).toFixed(2) }}
    </template>
    <template #cell-lucro="{ value }">
      <span :class="Number(value) >= 0 ? 'text-emerald-600' : 'text-red-600'">
        R$ {{ Number(value).toFixed(2) }}
      </span>
    </template>
    <template #cell-cancelada="{ value }">
      <span
        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
        :class="value ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
      >
        {{ value ? 'Cancelada' : 'Ativa' }}
      </span>
    </template>
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
    <template #actions="{ row }">
      <BaseButton
        v-if="!row.cancelada"
        variant="danger"
        @click="$emit('cancelar', row.id)"
      >
        Cancelar
      </BaseButton>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../ui/BaseTable.vue'
import BaseButton from '../ui/BaseButton.vue'

defineProps({
  vendas: { type: Array, default: () => [] },
})

defineEmits(['cancelar'])

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'cliente', label: 'Cliente' },
  { key: 'itens', label: 'Itens' },
  { key: 'total', label: 'Total' },
  { key: 'lucro', label: 'Lucro' },
  { key: 'cancelada', label: 'Status' },
  { key: 'created_at', label: 'Data' },
]

function formatDate(value) {
  if (!value) return '—'
  return new Date(value).toLocaleDateString('pt-BR')
}
</script>
