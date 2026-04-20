<template>
  <form @submit.prevent="onSubmit" class="space-y-4">
    <BaseInput
      v-model="cliente"
      label="Cliente"
      placeholder="Nome do cliente"
      :error="errors.cliente"
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
        :produto-options="filteredProdutoOptions(index)"
        :produtos-map="produtosMap"
        @remover="removerItem(index)"
      />

      <p v-if="items.length === 0 && !errors.itens" class="text-sm text-gray-500 text-center py-2">
        Nenhum item adicionado. Clique em "+ Adicionar Item".
      </p>
      <p v-if="errors.itens" class="text-sm text-red-500 text-center py-2 font-medium">
        {{ errors.itens }}
      </p>
    </div>

    <VendaResumo :total="total" :lucro-estimado="lucroEstimado" />

    <BaseButton type="submit" :loading="loading" :disabled="items.length === 0">
      Registrar Venda
    </BaseButton>
  </form>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
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
const errors = reactive({ cliente: '', itens: '' })
const { items, adicionarItem, removerItem, limpar, total, toPayload } = useItemList()

const produtoOptions = computed(() =>
  props.produtos
    .filter((p) => p.estoque > 0 && p.ativo)
    .map((p) => ({ value: p.id, label: `${p.nome} (Estoque: ${p.estoque})` })),
)

const produtosMap = computed(() => {
  const map = {}
  props.produtos.forEach((p) => { map[p.id] = p })
  return map
})

function filteredProdutoOptions(currentIndex) {
  const usedIds = items.value
    .filter((_, i) => i !== currentIndex)
    .map((item) => item.produto_id)
    .filter(Boolean)
  return produtoOptions.value.filter((opt) => !usedIds.includes(opt.value))
}

const lucroEstimado = computed(() =>
  items.value.reduce((sum, item) => {
    const produto = produtosMap.value[item.produto_id]
    if (!produto) return sum
    return sum + (item.preco_unitario - produto.custo_medio) * item.quantidade
  }, 0),
)

function onSubmit() {
  errors.cliente = ''
  errors.itens = ''

  if (!cliente.value.trim()) {
    errors.cliente = 'Informe o nome do cliente.'
  }
  if (items.value.length === 0) {
    errors.itens = 'Adicione pelo menos um item.'
  }
  if (errors.cliente || errors.itens) return

  emit('submit', {
    cliente: cliente.value,
    produtos: toPayload(),
  })

  cliente.value = ''
  limpar()
}
</script>
