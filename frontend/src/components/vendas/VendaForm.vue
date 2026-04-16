<template>
  <form @submit.prevent="onSubmit" class="space-y-4">
    <BaseInput
      v-model="cliente"
      label="Cliente"
      placeholder="Nome do cliente"
    />

    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-gray-700">Itens da Venda</span>
        <BaseButton variant="secondary" @click="adicionarItem()">
          + Adicionar Item
        </BaseButton>
      </div>

      <VendaItemRow
        v-for="(item, index) in items"
        :key="index"
        :item="item"
        :produto-options="produtoOptions"
        @remover="removerItem(index)"
      />

      <p v-if="items.length === 0" class="text-sm text-gray-500 text-center py-2">
        Nenhum item adicionado. Clique em "+ Adicionar Item".
      </p>
    </div>

    <VendaResumo :total="total" :lucro-estimado="lucroEstimado" />

    <BaseButton type="submit" :loading="loading" :disabled="items.length === 0">
      Registrar Venda
    </BaseButton>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useItemList } from '../../composables/useItemList'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'
import VendaItemRow from './VendaItemRow.vue'
import VendaResumo from './VendaResumo.vue'

const emit = defineEmits(['submit'])
const props = defineProps({
  produtos: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

const cliente = ref('')
const { items, adicionarItem, removerItem, limpar, total, toPayload } = useItemList()

const produtoOptions = computed(() =>
  props.produtos
    .filter((p) => p.estoque > 0)
    .map((p) => ({ value: p.id, label: `${p.nome} (Estoque: ${p.estoque})` })),
)

const produtosMap = computed(() => {
  const map = {}
  props.produtos.forEach((p) => { map[p.id] = p })
  return map
})

const lucroEstimado = computed(() =>
  items.value.reduce((sum, item) => {
    const produto = produtosMap.value[item.produto_id]
    if (!produto) return sum
    return sum + (item.preco_unitario - produto.custo_medio) * item.quantidade
  }, 0),
)

function onSubmit() {
  if (!cliente.value || items.value.length === 0) return

  emit('submit', {
    cliente: cliente.value,
    produtos: toPayload(),
  })

  cliente.value = ''
  limpar()
}
</script>
