<template>
  <div class="flex items-end gap-3">
    <BaseSelect
      v-model="item.produto_id"
      label="Produto"
      :options="produtoOptions"
      placeholder="Selecione um produto"
      class="flex-1"
      @update:model-value="onProdutoChange"
    />
    <BaseInput
      v-model="item.quantidade"
      label="Quantidade"
      type="number"
      min="1"
      class="w-28"
    />
    <BaseInput
      v-model="item.preco_unitario"
      label="Preço Unitário (R$)"
      type="number"
      step="0.01"
      min="0.01"
      class="w-36"
    />
    <BaseButton variant="danger" @click="$emit('remover')">
      ✕
    </BaseButton>
  </div>
</template>

<script setup>
import BaseSelect from '../ui/BaseSelect.vue'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'

const props = defineProps({
  item: { type: Object, required: true },
  produtoOptions: { type: Array, default: () => [] },
  produtosMap: { type: Object, default: () => ({}) },
})

defineEmits(['remover'])

function onProdutoChange(produtoId) {
  const produto = props.produtosMap[produtoId]
  if (produto) {
    props.item.preco_unitario = produto.preco_venda
  }
}
</script>
