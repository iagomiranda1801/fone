<template>
  <BaseTable :columns="columns" :rows="produtos" empty-text="Nenhum produto cadastrado.">
    <template #cell-custo_medio="{ value }">
      R$ {{ Number(value).toFixed(2) }}
    </template>
    <template #cell-preco_venda="{ value }">
      R$ {{ Number(value).toFixed(2) }}
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../ui/BaseTable.vue'

defineProps({
  produtos: { type: Array, default: () => [] },
})

const columns = [
  { key: 'id', label: 'ID' },
  { key: 'nome', label: 'Nome' },
  { key: 'custo_medio', label: 'Custo Médio' },
  { key: 'preco_venda', label: 'Preço Venda' },
  { key: 'estoque', label: 'Estoque' },
]
</script>
