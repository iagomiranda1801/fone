<template>
  <form @submit.prevent="onSubmit" class="space-y-4">
    <BaseInput
      v-model="fornecedor"
      label="Fornecedor"
      placeholder="Nome do fornecedor"
    />

    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-gray-700">Itens da Compra</span>
        <BaseButton variant="secondary" @click="adicionarItem()">
          + Adicionar Item
        </BaseButton>
      </div>

      <CompraItemRow
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

    <div class="flex items-center justify-between pt-2">
      <span class="text-sm font-semibold text-gray-700">
        Total: R$ {{ total.toFixed(2) }}
      </span>
      <BaseButton type="submit" :loading="loading" :disabled="items.length === 0">
        Registrar Compra
      </BaseButton>
    </div>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useItemList } from '../../composables/useItemList'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'
import CompraItemRow from './CompraItemRow.vue'

const emit = defineEmits(['submit'])
const props = defineProps({
  produtos: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

const fornecedor = ref('')
const { items, adicionarItem, removerItem, limpar, total, toPayload } = useItemList()

const produtoOptions = computed(() =>
  props.produtos.map((p) => ({ value: p.id, label: `${p.nome} (Estoque: ${p.estoque})` })),
)

function onSubmit() {
  if (!fornecedor.value || items.value.length === 0) return

  emit('submit', {
    fornecedor: fornecedor.value,
    produtos: toPayload(),
  })

  fornecedor.value = ''
  limpar()
}
</script>
